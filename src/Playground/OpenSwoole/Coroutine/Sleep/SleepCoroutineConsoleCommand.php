<?php

declare(strict_types=1);

namespace App\Playground\OpenSwoole\Coroutine\Sleep;

use OpenSwoole\Coroutine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:open-swoole:sleep-coroutine')]
final class SleepCoroutineConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Coroutine::run(static function () use ($output) {
            Coroutine::create(static function () use ($output) {
                $output->writeln('Coroutine 1 - sleep for 3 seconds');

                Coroutine::sleep(3);

                $output->writeln('Coroutine 1 - done');
            });

            Coroutine::create(static function () use ($output) {
                $output->writeln('Coroutine 2 - sleep for 3 seconds');

                Coroutine::sleep(3);

                $output->writeln('Coroutine 2 - done');
            });
        });

        $output->writeln('OK');

        return 0;
    }
}
