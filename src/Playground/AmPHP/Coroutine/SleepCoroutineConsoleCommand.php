<?php

declare(strict_types=1);

namespace App\Playground\AmPHP\Coroutine;

use Amp\Future;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Amp\async;
use function Amp\delay;
use function range;

#[AsCommand('app:amphp:sleep-coroutine')]
final class SleepCoroutineConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Future<int> $future1 */

        // This function is queued for the event loop
        $future1 = async(function () use ($output): int {
            foreach (range(1, 5) as $item) {
                echo 'Hallelujah! ';

                delay(1);
            }

            $output->writeln(PHP_EOL.'First finished');

            return 1;
        });
        /** @var Future<int> $future2 */
        // function is queued for the event loop
        $future2 = async(function () use ($output): int {
            foreach (range(1, 5) as $item) {
                echo 'Amen! ';

                delay(0.5);
            }

            $output->writeln(PHP_EOL.'Second finished');

            return 2;
        });

        // function is queued for the event loop
        $future3 = async(function () use ($output): never {
            $output->writeln('||The exception is thrown, yet it doesn\'t mess up the rest of futures||');

            throw new RuntimeException();
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

        try {
            $future3->await();
        } catch (Exception) {
            $output->writeln('The exception could be caught on the await');
        }

        return 0;
    }
}
