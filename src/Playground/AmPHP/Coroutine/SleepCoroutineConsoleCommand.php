<?php

declare(strict_types=1);

namespace App\Playground\AmPHP\Coroutine;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Amp\async;
use function Amp\delay;

#[AsCommand('app:amphp:sleep-coroutine')]
final  class SleepCoroutineConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // function is queued for the event loop
        $future1 = async(function () use ($output) {
            foreach (range(1, 5) as $item) {
                echo 'Hallelujah! ';

                delay(1);
            }

            $output->writeln(PHP_EOL.'First finished');

            return 1;
        });
        // function is queued for the event loop
        $future2 = async(function () use ($output) {
            foreach (range(1, 5) as $item) {
                echo 'Amen! ';

                delay(0.5);
            }

            $output->writeln(PHP_EOL.'Second finished');

            return 2;
        });

        $output->writeln('Start');

        // Event Loop gets the control on the first await
        $one = $future1->await();
        $output->writeln('First completed: '.$one);

        // the underlying function has already been executed long before it is awaited here
        // (actually it is finished already, since first function has taken longer time than it)
        // yet, it is necessary to await it in order "to be sure" it has completed (or to get the result)
        $two = $future2->await();
        $output->writeln('Second completed: '.$two);

        return 0;
    }
}
