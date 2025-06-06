<?php

declare(strict_types=1);

namespace App\Support\Temporal\Timer;

enum TimerState: int
{
    case SCHEDULED = 0;
    case STARTED = 1;
    case FIRED = 2;
    case CANCELLED = 3;
}
