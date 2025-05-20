<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use App\Playground\Temporal\Booking\Workflow\TripBookingWorkflow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

/**
 * Run temporal server on your host machine as:
 * temporal server start-dev --ip 0.0.0.0
 */
#[AsCommand('app:temporal:trip-booking')]
final class TripBookingWorkflowConsoleCommand extends Command
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
            'fail',
            null,
            InputOption::VALUE_REQUIRED,
            'Fail flag (none, car, flight, hotel)',
            'none'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $workflow = $this->workflowClient->newWorkflowStub(
            TripBookingWorkflow::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(3),
        );

        $io->info('Starting Trip Booking Workflow...');

        $failFlag = match ($input->getOption('fail')) {
            'car' => FailFlag::CAR_RESERVATION,
            'flight' => FailFlag::FLIGHT_RESERVATION,
            'hotel' => FailFlag::HOTEL_RESERVATION,
            'after' => FailFlag::AFTER_ALL,
            default => FailFlag::NONE,
        };

        [$carReservationId, $flightReservationId, $hotelReservationId] = $workflow->run($failFlag);

        $io->success('Trip Booking completed successfully!');
        $io->section('Reservations:');
        $io->listing([
            sprintf('<fg=yellow>Car</fg=yellow>: %s', $carReservationId),
            sprintf('<fg=yellow>Flight</fg=yellow>: %s', $flightReservationId),
            sprintf('<fg=yellow>Hotel</fg=yellow>: %s', $hotelReservationId),
        ]);

        return Command::SUCCESS;
    }
}
