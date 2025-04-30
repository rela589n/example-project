<?php

declare(strict_types=1);

namespace App\Playground\AmPHP\HelloWorld;

use function Amp\Future\awaitAll;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Amp\async;
use function Amp\delay;

#[AsCommand('app:amphp:hello-world')]
final class AmphpHelloWorldConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $future1 = async(function () use ($output): void {
            $output->write('Hello ');

            delay(1);

            $output->write('From ');
        });

        $future2 = async(function () use ($output): void {
            delay(0.5);

            $output->write('World ');

            delay(1);

            $output->writeln('the future!');
        });

        // when awaiting, every 0.5 seconds the next write is executed
        awaitAll([$future1, $future2]);

        return 0;
    }
}
