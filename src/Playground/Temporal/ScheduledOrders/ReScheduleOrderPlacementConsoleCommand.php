<?php

declare(strict_types=1);

namespace App\Playground\Temporal\ScheduledOrders;

use App\Playground\Temporal\ScheduledOrders\Workflow\PlaceScheduledOrderWorkflow;
use Carbon\CarbonImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;

#[AsCommand('app:temporal:scheduled-order:re-schedule', 'Re-schedule a scheduled order placement')]
final class ReScheduleOrderPlacementConsoleCommand extends Command
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
            'order-id',
            InputArgument::REQUIRED,
            'Order ID',
        );

        $this->addOption(
            'interval',
            null,
            InputOption::VALUE_REQUIRED,
            'The time interval at which the order should be placed (in seconds from now)',
            '10',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $orderId */
        $orderId = $input->getArgument('order-id');

        /** @var string $placementInterval */
        $placementInterval = $input->getOption('interval');
        $placementInterval = (int)$placementInterval;

        $io->writeln(sprintf('Re-scheduling order to be placed in %d seconds', $placementInterval));

        /** @var PlaceScheduledOrderWorkflow $workflow */
        $workflow = $this->workflowClient->newRunningWorkflowStub(PlaceScheduledOrderWorkflow::class, $orderId.':schedule');

        $placementDate = CarbonImmutable::now()->addSeconds($placementInterval);

        $workflow->changePlacementDate($placementDate);

        $io->success(sprintf('Order %s has been re-scheduled to be placed at %s', $orderId, $placementDate->toDateTimeString()));

        return Command::SUCCESS;
    }
}

