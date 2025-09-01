<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Car;

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

#[ActivityInterface('ReserveCar.')]
#[AssignWorker(TripBookingWorkflow::TASK_QUEUE)]
#[WithMonologChannel(TripBookingWorkflow::TASK_QUEUE)]
final readonly class ReserveCarActivity
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
                // It's good to have a "Schedule-To-Start" timeout for the first Activity,
                // since it will fail right away if the server is overloaded (better not to make the new load at all)
                ->withScheduleToStartTimeout(3)
                // "Start-To-Close" timeout is required (execution)
                ->withStartToCloseTimeout(1)
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::seconds(2))
                        ->withBackoffCoefficient(2)
                        ->withMaximumInterval(CarbonInterval::seconds(5))
                        ->withMaximumAttempts(3),
                ),
        );
    }

    #[ActivityMethod]
    public function process(FailOptions $failOptions): string
    {
        $this->logger->info('Reserving the car (attempt {attempt})', [
            'attempt' => Activity::getInfo()->attempt,
        ]);

        if ($failOptions->shouldFail(BookFailFlag::CAR_RESERVATION, Activity::getInfo())) {
            throw new RuntimeException('Could not reserve car');
        }

        $carReservationId = Uuid::v7()->toRfc4122();

        $this->logger->info('Car reserved: {id}', [
            'id' => $carReservationId,
        ]);

        return $carReservationId;
    }

    #[ActivityMethod]
    public function cancel(string $reservationId): void
    {
        $this->logger->info('Car reservation cancelled: {id}', [
            'id' => $reservationId,
        ]);
    }
}
