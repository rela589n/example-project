<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription;

use App\Playground\Temporal\Subscription\Workflow\SubscriptionStatus;
use App\Playground\Temporal\Subscription\Workflow\SubscriptionWorkflow;
use App\Playground\Temporal\Subscription\Workflow\SubscriptionWorkflowId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:subscription:start')]
final class StartSubscriptionWorkflowConsoleCommand extends Command
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
            'userId',
            InputArgument::REQUIRED,
            'User ID'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $userId */
        $userId = $input->getArgument('userId');

        $workflow = $this->workflowClient->newWorkflowStub(
            SubscriptionWorkflow::class,
            WorkflowOptions::new()
                ->withWorkflowId(SubscriptionWorkflowId::fromUserId($userId)->getId()),
        );

        $workflowRun = $this->workflowClient->start($workflow, SubscriptionStatus::start($userId));

        $io->success(sprintf('Subscription workflow started for user ID: %s', $userId));

        $io->listing([
            sprintf("<fg=green>WorkflowID:</>\t<fg=yellow>%s</>", $workflowRun->getExecution()->getID()),
            sprintf("<fg=green>RunID:</>\t<fg=yellow>%s</>", $workflowRun->getExecution()->getRunID()),
        ]);

        return Command::SUCCESS;
    }
}
