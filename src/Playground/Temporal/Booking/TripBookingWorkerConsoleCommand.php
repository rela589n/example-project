<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking;

use App\Playground\Temporal\Booking\Workflow\Car\ReserveCarActivity;
use App\Playground\Temporal\Booking\Workflow\Flight\BookFlightActivity;
use App\Playground\Temporal\Booking\Workflow\Hotel\BookHotelActivity;
use App\Playground\Temporal\Booking\Workflow\TripBookingWorkflow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Temporal\WorkerFactory;

/**
 * To run it, use the following roadrunner configuration:
 *
 * temporal:
 *     address: "host.docker.internal:7233"
 *     activities:
 *         debug: true
 *         command: "bin/console app:temporal:trip-booking:worker"
 *         num_workers: 1 # defaults to 1, when debug
 */
#[AsCommand(name: 'app:temporal:trip-booking:worker')]
final class TripBookingWorkerConsoleCommand extends Command
{
    public function __construct(
        #[AutowireLocator([ReserveCarActivity::class, BookFlightActivity::class, BookHotelActivity::class])]
        private readonly ServiceProviderInterface $container,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factory = WorkerFactory::create();

        $worker = $factory->newWorker();

        // Register the workflow
        $worker->registerWorkflowTypes(TripBookingWorkflow::class);

        // Register the activities
        array_map(
            fn (string $activityClassName) => $worker->registerActivity($activityClassName, fn () => $this->container->get($activityClassName)),
            array_keys($this->container->getProvidedServices()),
        );

        $factory->run();

        return Command::SUCCESS;
    }
}
