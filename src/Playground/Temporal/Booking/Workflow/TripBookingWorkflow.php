<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow;

use App\Playground\Temporal\Booking\Workflow\Car\ReserveCarActivity;
use App\Playground\Temporal\Booking\Workflow\Flight\BookFlightActivity;
use App\Playground\Temporal\Booking\Workflow\Hotel\BookHotelActivity;
use Generator;
use LogicException;
use Temporal\Activity;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Throwable;

#[WorkflowInterface]
final readonly class TripBookingWorkflow
{
    private Workflow\Saga $saga;

    /** @var ActivityProxy&ReserveCarActivity */
    private ActivityProxy $reserveCar;

    /** @var ActivityProxy&BookFlightActivity */
    private ActivityProxy $bookFlight;

    /** @var ActivityProxy&BookHotelActivity */
    private ActivityProxy $bookHotel;

    public function __construct()
    {
        $this->saga = new Workflow\Saga()
            ->setParallelCompensation(true);

        $this->reserveCar = Workflow::newActivityStub(
            ReserveCarActivity::class,
            Activity\ActivityOptions::new()
                ->withStartToCloseTimeout(2),
        );

        $this->bookFlight = Workflow::newActivityStub(
            BookFlightActivity::class,
            Activity\ActivityOptions::new()
                ->withStartToCloseTimeout(2),
        );

        $this->bookHotel = Workflow::newActivityStub(
            BookHotelActivity::class,
            Activity\ActivityOptions::new()
                ->withStartToCloseTimeout(2),
        );
    }

    #[WorkflowMethod]
    public function run(FailFlag $flag): Generator
    {
        try {
            return yield $this->runBooking($flag);
        } catch (Throwable $e) {
            yield $this->saga->compensate();

            throw $e;
        }
    }

    private function runBooking(FailFlag $flag): Generator
    {
        $carReservationId = yield $this->reserveCarForTrip($flag);

        $flightReservationId = yield $this->reserveFlight($flag);

        $hotelReservationId = yield $this->reserveHotel($flag, $flightReservationId);

        if (FailFlag::AFTER_ALL === $flag) {
            throw new LogicException('After all, something went wrong');
        }

        return [$carReservationId, $flightReservationId, $hotelReservationId];
    }

    private function reserveCarForTrip(FailFlag $flag): Generator
    {
        $carReservationId = yield $this->reserveCar->process($flag);
        $this->saga->addCompensation(fn () => $this->reserveCar->cancel($carReservationId));

        return $carReservationId;
    }

    private function reserveFlight(FailFlag $flag): Generator
    {
        $flightReservationId = yield $this->bookFlight->process($flag);
        $this->saga->addCompensation(fn () => $this->bookFlight->cancel($flightReservationId));

        return $flightReservationId;
    }

    private function reserveHotel(FailFlag $flag, $flightReservationId): Generator
    {
        $hotelReservationId = yield $this->bookHotel->process($flag, $flightReservationId);
        $this->saga->addCompensation(fn () => $this->bookHotel->cancel($hotelReservationId));

        return $hotelReservationId;
    }
}
