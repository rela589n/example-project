<?php

declare(strict_types=1);

namespace App\Playground\Swoole\Postgres\Basic;

use Doctrine\DBAL\Connection;
use Swoole\Coroutine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand('app:swoole:postgres-async')]
final class SwoolePostgresAsyncQueriesConsoleCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $measure = (new Stopwatch())->start('queries');

        // if we wrap this run in transaction(), it will result
        // in "another command is already in progress" failure
        Coroutine\run(function () use ($output) {
            $output->writeln('Making 2 postgresql requests');

            Coroutine\go(function () use ($output) {
                $output->writeln('First query started');

                $result = $this->runQuery(4);

                $output->writeln('First query completed: '.$result);
            });

            Coroutine\go(function () use ($output) {
                $output->writeln('Second query started');

                $result = $this->runQuery(3);

                $output->writeln('Second query completed: '.$result);
            });
        });

        $measure->stop();
        $output->writeln('It took: '.$measure->getDuration().' ms');

        return 0;
    }

    private function runQuery(int $sleepTime): int
    {
        $statement = $this->connection->prepare('SELECT :result as result, pg_sleep(:sleep_time)');
        $statement->bindValue('result', $sleepTime);
        $statement->bindValue('sleep_time', $sleepTime);

        return (int)$statement->executeQuery()->fetchOne(); // @phpstan-ignore-line
    }
}
