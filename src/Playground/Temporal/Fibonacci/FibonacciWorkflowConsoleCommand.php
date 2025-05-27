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
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:fibonacci')]
final class FibonacciWorkflowConsoleCommand extends Command
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
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Fibonacci limit',
            10
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)$input->getOption('limit');

        $workflow = $this->workflowClient->newWorkflowStub(
            FibonacciNumbersWorkflow::class,
            WorkflowOptions::new(),
        );

        $run = $this->workflowClient->start($workflow, $limit);

        $io->success('Workflow Started');
        $io->listing([
            sprintf('<fg=green>WorkflowID:</> <fg=yellow>%s</>', $run->getExecution()->getID()),
            sprintf('<fg=green>RunID:</> <fg=yellow>%s</>', $run->getExecution()->getRunID()),
        ]);

        return Command::SUCCESS;
    }
}

