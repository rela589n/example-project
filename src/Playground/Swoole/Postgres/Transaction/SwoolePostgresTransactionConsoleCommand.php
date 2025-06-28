<?php

declare(strict_types=1);

namespace App\Playground\Swoole\Postgres\Transaction;

use function Swoole\Coroutine\go;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Swoole\Coroutine\run;

#[AsCommand('app:swoole:postgres-transaction')]
final class SwoolePostgresTransactionConsoleCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();

        // Not having savepoints would result in thrown error
        // $this->connection->setNestTransactionsWithSavepoints(false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        run(function () use ($output): void {
            $output->writeln('Making 2 postgresql requests');

            go(function () use ($output): void {
                $output->writeln('First query started');

                $result = $this->connection->transactional(fn () => $this->runQuery(1));

                $output->writeln('First query completed: '.$result);
            });

            go(function () use ($output): void {
                $output->writeln('Second query started');

                // this one either uses the same transaction or just fails with exception !
                $result = $this->connection->transactional(fn () => $this->runQuery(2));

                $output->writeln('Second query completed: '.$result);
            });
        });

        return 0;
    }

    private function runQuery(int $result): int
    {
        $statement = $this->connection->prepare('SELECT :result as result, pg_sleep(:sleep_time)');
        $statement->bindValue('result', $result);
        $statement->bindValue('sleep_time', 3);

        return (int)$statement->executeQuery()->fetchOne(); // @phpstan-ignore-line
    }
}
