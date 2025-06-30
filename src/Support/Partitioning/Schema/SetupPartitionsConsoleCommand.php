<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Schema;

use Amp\CompositeException;
use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresConnectionPool;
use Amp\Sql\SqlConnectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Amp\async;
use function Amp\Future\awaitAll;
use function Amp\Future\awaitAnyN;

#[AsCommand(name: 'app:partitioning:setup-partitions', description: 'Setup partitions')]
final class SetupPartitionsConsoleCommand extends Command
{
    private const int CONNECTIONS = 256;

    protected function configure(): void
    {
        $this
            ->addArgument('source-table', InputArgument::REQUIRED, 'Source (key) table')
            ->addArgument('target-table', InputArgument::REQUIRED, 'Target (partitioned) table')
            ->addOption('connections', 'c', InputOption::VALUE_OPTIONAL, 'Number of concurrent connections', self::CONNECTIONS)
            ->addOption('start-at', null, InputOption::VALUE_REQUIRED, 'Number of iterations to skip', 0)
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command sets up partitions.

Usage example:
  <info>bin/console app:partitioning:setup-partitions accounting_accounts_not_partitioned accounting_account_transactions -c 256 --start-at=0 -vv</info>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $config = PostgresConfig::fromString("host=postgresql user=postgres db=project_db_test password=qwerty");

        /** @var string $connections */
        $connections = $input->getOption('connections');
        $connections = (int)$connections;

        $pool = new PostgresConnectionPool($config, $connections, resetConnections: false);

        $sourceTable = $input->getArgument('source-table');
        $targetTable = $input->getArgument('target-table');
        /** @var string $startAt */
        $startAt = $input->getOption('start-at');
        $startAt = (int)$startAt;

        $io->info(sprintf('Selecting %s...', $sourceTable));

        $results = $pool->execute(sprintf('SELECT sequence_id, id FROM %s ORDER BY sequence_id', $sourceTable));

        $io->info(sprintf('Creating partitions for %s...', $targetTable));

        $progressBar = new ProgressBar($output, (int)($results->getRowCount()));
        $progressBar->setFormat(ProgressBar::FORMAT_DEBUG);
        $progressBar->start(startAt: $startAt);

        if ($startAt > 0) {
            $io->info(sprintf('Starting from %d iteration', $startAt));
        }

        $it = 0;
        $futures = [];
        foreach ($results as ['sequence_id' => $sequenceId, 'id' => $id]) {
            // Skip the specified number of iterations
            if ($it < $startAt) {
                ++$it;

                continue;
            }

            $futures[] = async(
                fn () => $pool->execute(sprintf(
                    <<<SQL
                CREATE TABLE
                    IF NOT EXISTS {$targetTable}_p%s
                    PARTITION OF {$targetTable} 
                        FOR VALUES IN ('%s');
                SQL,
                    $sequenceId,
                    $id,
                )),
            );

            ++$it;

            if ($it % $connections === 0) {
                try {
                    awaitAnyN(count($futures), $futures);
                } catch (CompositeException $e) {
                    $reasons = $e->getReasons();
                    $connectionException = reset($reasons);

                    if (!$connectionException instanceof SqlConnectionException) {
                        throw $connectionException;
                    }

                    throw $connectionException->getPrevious();
                }
            }

            $progressBar->advance(1);
        }

        if (!empty($futures)) {
            awaitAll($futures);
        }

        $progressBar->finish();

        $io->success(sprintf('All partitions for %s created successfully.', $targetTable));

        return Command::SUCCESS;
    }
}
