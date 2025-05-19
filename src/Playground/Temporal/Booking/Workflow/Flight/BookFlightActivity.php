<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Flight;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface('BookFlight.')]
final readonly class BookFlightActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function process(FailFlag $flag): string
    {
        $this->logger->info('Booking the flight');

        if (FailFlag::FLIGHT_RESERVATION === $flag) {
            throw new LogicException('Could not book flight');
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
