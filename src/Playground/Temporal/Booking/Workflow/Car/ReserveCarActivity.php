<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow\Car;

use App\Playground\Temporal\Booking\Workflow\FailFlag;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface('ReserveCar.')]
final readonly class ReserveCarActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function process(FailFlag $flag): string
    {
        $this->logger->info('Reserving the car');

        if (FailFlag::CAR_RESERVATION === $flag) {
            throw new LogicException('Could not reserve car');
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
