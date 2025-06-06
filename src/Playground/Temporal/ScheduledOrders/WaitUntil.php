<?php

declare(strict_types=1);

namespace App\Playground\Temporal\ScheduledOrders;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeImmutable;
use Generator;
use Temporal\Workflow;

final readonly class WaitUntil
{
    public function __construct(
        /** @var Closure(): DateTimeImmutable */
        private Closure $waitUntil,
    ) {
    }

    public function __invoke(): Generator
    {
        do {
            $waitUntil = ($this->waitUntil)();

            /** @var CarbonImmutable $currentDateTime */
            $currentDateTime = yield $this->getCurrentDateTime();

            $waitInterval = $currentDateTime->diff($waitUntil);

            if ($waitInterval->invert) {
                // negative interval, it's the time already
                break;
            }

            $isTimeoutReached = !yield Workflow::awaitWithTimeout(
                $waitInterval,
                fn (): bool => ($this->waitUntil)() != $waitUntil,
            );
        } while (!$isTimeoutReached);
    }

    private function getCurrentDateTime(): Generator
    {
        /** @var int $currentTimestamp */
        $currentTimestamp = yield Workflow::sideEffect(static fn (): int => CarbonImmutable::now()->getTimestamp());

        return CarbonImmutable::createFromTimestamp($currentTimestamp);
    }
}
