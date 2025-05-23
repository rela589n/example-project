<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Flight;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use Carbon\CarbonInterval;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;

#[ActivityInterface('BookFlight.')]
final readonly class BookFlightActivity
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
    public function process(FailFlag $flag): string
    {
        $this->logger->info('Booking the flight (attempt {attempt})', [
            'attempt' => Activity::getInfo()->attempt,
        ]);

        if (FailFlag::FLIGHT_RESERVATION === $flag) {
            throw new LogicException('Could not book flight');
        }

        if (random_int(0, 1) !== 0) {
            throw new RuntimeException('Temporary failure');
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
