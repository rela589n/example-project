<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Flight;

use App\Playground\Temporal\Booking\Workflow\BookFailFlag;
use App\Playground\Temporal\Booking\Workflow\FailOptions;
use App\Playground\Temporal\Booking\Workflow\TripBookingWorkflow;
use Carbon\CarbonInterval;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('BookFlight.')]
#[AssignWorker(TripBookingWorkflow::TASK_QUEUE)]
#[WithMonologChannel(TripBookingWorkflow::TASK_QUEUE)]
final readonly class BookFlightActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(1)
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::seconds(2))
                        ->withBackoffCoefficient(2)
                        ->withMaximumInterval(CarbonInterval::hour())
                        ->withMaximumAttempts(3),
                ),
        );
    }

    #[ActivityMethod]
    public function process(FailOptions $failOptions): string
    {
        $this->logger->info('Booking the flight (attempt {attempt})', [
            'attempt' => Activity::getInfo()->attempt,
        ]);

        if ($failOptions->shouldFail(BookFailFlag::FLIGHT_RESERVATION, Activity::getInfo())) {
            throw new RuntimeException('Could not book flight');
        }

        $flightReservationId = Uuid::v7()->toRfc4122();

        $this->logger->info('Flight reserved: {id}', [
            'id' => $flightReservationId,
        ]);

        return $flightReservationId;
    }

    #[ActivityMethod]
    public function cancel(string $reservationId): void
    {
        $this->logger->info('Flight reservation cancelled: {id}', [
            'id' => $reservationId,
        ]);
    }
}
