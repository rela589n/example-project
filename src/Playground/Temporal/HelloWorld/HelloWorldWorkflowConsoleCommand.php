<?php

declare(strict_types=1);

namespace App\Playground\Temporal\HelloWorld;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

use function sprintf;

#[AsCommand('app:temporal:hello')]
final class HelloWorldWorkflowConsoleCommand extends Command
{
    private WorkflowClientInterface $workflowClient;

    public function __construct()
    {
        parent::__construct();

        // Run temporal server on your host machine as:
        // temporal server start-dev --ip 0.0.0.0
        $address = 'host.docker.internal:7233';

        $serviceClient = ServiceClient::create($address);

        $this->workflowClient = WorkflowClient::create($serviceClient);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('name', InputArgument::OPTIONAL, 'Name to greet', 'World');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            HelloWorldWorkflow::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(1),
        );

        /** @var string $name */
        $name = $input->getArgument('name');

        $greeting = $workflow->greet($name);

        $output->writeln(sprintf("Result:\n<info>%s</info>", $greeting));

        return Command::SUCCESS;
    }
}
