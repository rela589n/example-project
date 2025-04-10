<?php

declare(strict_types=1);

namespace App\Playground\Revolt\Timers;

use Revolt\EventLoop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:revolt:timers')]
final class EventLoopTimersConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        EventLoop::queue(
            static function () use ($output): void {
                // repeated callbacks are started after the interval period has elapsed
                $repeatCallbackId = EventLoop::repeat(1, static function () use ($output): void {
                    $output->writeln('Repeat');
                });

                // delayed callbacks are executed after the delay time has elapsed
                EventLoop::delay(5, static function () use ($output, $repeatCallbackId): void {
                    EventLoop::cancel($repeatCallbackId);

                    EventLoop::defer(static function () use ($output): void {
                        $output->writeln('Loop completed (last tick)');
                    });

                    EventLoop::queue(static function () use ($output): void {
                        $output->writeln('Loop finished');
                    });
                });

                // deferred callbacks are executed on the next tick
                EventLoop::defer(static function () use ($output): void {
                    $output->writeln('Loop started');
                });

                // queued callbacks are executed in the current tick
                EventLoop::queue(static function () use ($output): void {
                    $output->writeln('Start loop');
                });

                $output->writeln('Before start');
            },
        );

        EventLoop::run();

        return 0;
    }
}
