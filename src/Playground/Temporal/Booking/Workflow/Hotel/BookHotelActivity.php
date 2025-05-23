<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Hotel;

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

#[ActivityInterface('BookHotel.')]
final readonly class BookHotelActivity
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
                        ->withMaximumAttempts(3),
                ),
        );
    }

    #[ActivityMethod]
    public function process(FailFlag $flag, string $flightReservationId): string
    {
        $this->logger->info('Booking the hotel (flight id: {flightReservationId}, attempt: {attempt})', [
            'flightReservationId' => $flightReservationId,
            'attempt' => Activity::getInfo()->attempt,
        ]);

        if (FailFlag::HOTEL_RESERVATION === $flag) {
            throw new \LogicException('Could not book hotel');
        }

        if (random_int(0, 1) !== 0) {
            throw new RuntimeException('Temporary failure');
        }

        $hotelReservationId = Uuid::v7()->toRfc4122();

        $this->logger->info('Hotel reserved: {id}', [
            'id' => $hotelReservationId,
        ]);

        return $hotelReservationId;
    }

    #[ActivityMethod]
    public function cancel(string $reservationId): void
    {
        $this->logger->info('Hotel reservation cancelled: {id}', [
            'id' => $reservationId,
        ]);
    }
}
