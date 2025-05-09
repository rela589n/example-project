<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\WorkerFactory;

/**
 * To run it, use the following roadrunner configuration:
 *
 * temporal:
 *     address: "host.docker.internal:7233"
 *     activities:
 *         debug: true
 *         command: "bin/console app:temporal:quote-of-the-day:worker"
 */
#[AsCommand(name: 'app:temporal:quote-of-the-day:worker')]
class QuoteOfTheDayWorkerConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factory = WorkerFactory::create();

        $worker = $factory->newWorker();

        $worker->registerWorkflowTypes(QuoteOfTheDayWorkflow::class);
        $worker->registerActivity(QuoteOfTheDayActivity::class);

        $factory->run();

        return Command::SUCCESS;
    }
}
