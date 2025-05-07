<?php

declare(strict_types=1);

namespace App\Playground\Temporal\HelloWorld;

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
 *         command: "bin/console app:temporal:hello-world:worker"
 *         num_workers: 1
 */
#[AsCommand(name: 'app:temporal:hello-world:worker')]
class WorkerRunConsoleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factory = WorkerFactory::create();

        $worker = $factory->newWorker();

        $worker->registerWorkflowTypes(HelloWorldWorkflow::class);

        $factory->run();

        return Command::SUCCESS;
    }
}
