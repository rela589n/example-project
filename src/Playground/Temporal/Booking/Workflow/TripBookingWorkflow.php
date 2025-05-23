<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow;

use App\Playground\Temporal\Booking\Workflow\Car\ReserveCarActivity;
use App\Playground\Temporal\Booking\Workflow\Flight\BookFlightActivity;
use App\Playground\Temporal\Booking\Workflow\Hotel\BookHotelActivity;
use Generator;
use LogicException;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Throwable;

#[WorkflowInterface]
final readonly class TripBookingWorkflow
{
    private Workflow\Saga $saga;

    private ReserveCarActivity|ActivityProxy $reserveCar;

    private BookFlightActivity|ActivityProxy $bookFlight;

    private BookHotelActivity|ActivityProxy $bookHotel;

    public function __construct()
    {
        $this->saga = new Workflow\Saga()
            ->setParallelCompensation(true);

        $this->reserveCar = ReserveCarActivity::create();
        $this->bookFlight = BookFlightActivity::create();
        $this->bookHotel = BookHotelActivity::create();
    }

    #[WorkflowMethod]
    public function run(FailFlag $flag): Generator
    {
        try {
            return yield $this->processBooking($flag);
        } catch (Throwable $e) {
            yield $this->saga->compensate();

            throw $e;
        }
    }

    private function processBooking(FailFlag $flag): Generator
    {
        /** @var string $carReservationId */
        $carReservationId = yield $this->reserveCarForTrip($flag);

        /** @var string $flightReservationId */
        $flightReservationId = yield $this->reserveFlight($flag);

        /** @var string $hotelReservationId */
        $hotelReservationId = yield $this->reserveHotel($flag, $flightReservationId);

        if (FailFlag::AFTER_ALL === $flag) {
            throw new LogicException('After all, something went wrong');
        }

        return [$carReservationId, $flightReservationId, $hotelReservationId];
    }

    private function reserveCarForTrip(FailFlag $flag): Generator
    {
        /** @var string $carReservationId */
        $carReservationId = yield $this->reserveCar->process($flag);
        $this->saga->addCompensation(fn () => $this->reserveCar->cancel($carReservationId));

        return $carReservationId;
    }

    private function reserveFlight(FailFlag $flag): Generator
    {
        /** @var string $flightReservationId */
        $flightReservationId = yield $this->bookFlight->process($flag);
        $this->saga->addCompensation(fn () => $this->bookFlight->cancel($flightReservationId));

        return $flightReservationId;
    }

    private function reserveHotel(FailFlag $flag, string $flightReservationId): Generator
    {
        /** @var string $hotelReservationId */
        $hotelReservationId = yield $this->bookHotel->process($flag, $flightReservationId);
        $this->saga->addCompensation(fn () => $this->bookHotel->cancel($hotelReservationId));

        return $hotelReservationId;
    }
}
