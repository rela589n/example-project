<?php

declare(strict_types=1);

namespace App\Playground\Swoole\Coroutine\Sleep;

use function Swoole\Coroutine\run;
use Swoole\Coroutine;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:swoole:sleep-coroutine')]
final class SwooleSleepCoroutineConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        run(static function () use ($output): void {
            Coroutine::create(static function () use ($output): void {
                $output->write('Hello ');

                // note that this is the native php function that is usually blocking
                usleep(1_000_000);

                $output->write('from ');
            });

            Coroutine::create(static function () use ($output): void {
                usleep(500_000);

                $output->write('World ');

                usleep(1_000_000);

                $output->writeln('the coroutine!');
            });
        });

        return 0;
    }
}
