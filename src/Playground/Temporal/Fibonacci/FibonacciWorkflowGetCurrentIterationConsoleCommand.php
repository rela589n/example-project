<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Fibonacci;

use App\Playground\Temporal\Fibonacci\Workflow\FibonacciNumbersWorkflow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;

#[AsCommand('app:temporal:fibonacci:current-iteration')]
final class FibonacciWorkflowGetCurrentIterationConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'workflow-id',
            null,
            InputOption::VALUE_REQUIRED,
            'Workflow ID of the running Fibonacci workflow'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $workflowId */
        $workflowId = $input->getOption('workflow-id');

        if (!$workflowId) {
            $io->error('The --workflow-id option is required.');

            return Command::INVALID;
        }

        $workflow = $this->workflowClient->newRunningWorkflowStub(
            FibonacciNumbersWorkflow::class,
            $workflowId,
        );

        $iteration = $workflow->getIteration();

        $io->success("Current workflow iteration is: $iteration");

        return Command::SUCCESS;
    }
}

