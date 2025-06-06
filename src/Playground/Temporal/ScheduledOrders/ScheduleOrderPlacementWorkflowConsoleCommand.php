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
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:scheduled-order:schedule', 'Schedule a new order placement')]
final class ScheduleOrderPlacementWorkflowConsoleCommand extends Command
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
            InputArgument::OPTIONAL,
            'Order ID',
            'order' . random_int(1, 200),
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

        $io->writeln(sprintf('Placing order in %d seconds', $placementInterval));

        $workflow = $this->workflowClient
            ->newWorkflowStub(
                PlaceScheduledOrderWorkflow::class,
                WorkflowOptions::new()
                    ->withWorkflowId($orderId.':schedule'),
            );

        $placeAt = CarbonImmutable::now()->addSeconds($placementInterval);

        $workflowRun = $this->workflowClient->start($workflow, $orderId, $placeAt);

        $io->success(sprintf('Order %s has been scheduled to be placed at %s', $orderId, $placeAt->toDateTimeString()));

        $io->listing([
            sprintf("<fg=green>WorkflowID:</>\t<fg=yellow>%s</>", $workflowRun->getExecution()->getID()),
            sprintf("<fg=green>RunID:</>\t<fg=yellow>%s</>", $workflowRun->getExecution()->getRunID()),
        ]);

        return Command::SUCCESS;
    }
}
