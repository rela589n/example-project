<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Hotel;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface('BookHotel.')]
final readonly class BookHotelActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function process(FailFlag $flag, string $flightReservationId): string
    {
        $this->logger->info('Booking the hotel (flight id: {flightReservationId})', [
            'flightReservationId' => $flightReservationId,
        ]);

        if (FailFlag::HOTEL_RESERVATION === $flag) {
            throw new \LogicException('Could not book hotel');
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
