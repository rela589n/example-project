<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription;

use App\Playground\Temporal\Subscription\Workflow\SubscriptionWorkflowId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;

#[AsCommand('app:temporal:subscription:cancel')]
final class CancelSubscriptionWorkflowConsoleCommand extends Command
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

        $workflow = $this->workflowClient->newUntypedRunningWorkflowStub(
            SubscriptionWorkflowId::fromUserId($userId)->getId(),
        );

        $workflow->cancel();

        $io->success('Subscription workflow cancelled successfully.');

        return Command::SUCCESS;
    }
}
