<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Fibonacci;

use App\Playground\Temporal\Fibonacci\Workflow\FibonacciNumbersWorkflow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;

#[AsCommand('app:temporal:fibonacci:shift-limit')]
final class FibonacciWorkflowShiftLimitConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'limit',
            InputArgument::REQUIRED,
            'New Fibonacci limit',
        );

        $this->addOption(
            'workflow-id',
            null,
            InputOption::VALUE_REQUIRED,
            'Workflow ID of the running Fibonacci workflow',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $workflowId */
        $workflowId = $input->getOption('workflow-id');

        /** @var string $limit */
        $limit = $input->getArgument('limit');

        if (!$workflowId || !$limit) {
            $io->error('Both --workflow-id option and limit argument are required.');

            return Command::INVALID;
        }

        $workflow = $this->workflowClient->newRunningWorkflowStub(
            FibonacciNumbersWorkflow::class,
            $workflowId,
        );

        $workflow->shiftLimit((int)$limit);

        $io->success("Limit shifted to {$limit} for workflow {$workflowId}.");

        return Command::SUCCESS;
    }
}
