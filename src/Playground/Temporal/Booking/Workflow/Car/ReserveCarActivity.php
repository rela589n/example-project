<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Car;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use Carbon\CarbonInterval;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;

#[ActivityInterface('ReserveCar.')]
final readonly class ReserveCarActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(): self|ActivityProxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                // It's good to have "Schedule-To-Start" timeout for the first activity,
                // since if the server is overloaded, it's better not to make the new load
                ->withScheduleToStartTimeout(3)
                // "Start-To-Close" timeout is required
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
    public function process(FailFlag $flag): string
    {
        $activityInfo = Activity::getInfo();

        $this->logger->info('Reserving the car (attempt {attempt})', [
            'attempt' => $activityInfo->attempt,
        ]);

        if (FailFlag::CAR_RESERVATION === $flag) {
            throw new LogicException('Could not reserve car');
        }

        if (random_int(0, 2) !== 0) {
            throw new LogicException('Temporary failure');
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
