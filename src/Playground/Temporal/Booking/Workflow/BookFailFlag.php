<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow;

enum BookFailFlag: string
{
    case NONE = 'none';

    case CAR_RESERVATION = 'car_reservation';

    case FLIGHT_RESERVATION = 'flight_reservation';

    case HOTEL_RESERVATION = 'hotel_reservation';

    case RANDOM = 'random';

    case AFTER_ALL = 'after_all';
}
