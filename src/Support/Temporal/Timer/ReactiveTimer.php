<?php

declare(strict_types=1);

namespace App\Support\Temporal\Timer;

use Carbon\CarbonImmutable;
use Closure;
use Generator;
use Temporal\Workflow;

final class ReactiveTimer
{
    private bool $isCompleted = false;

    public function __construct(
        /** @var Closure(): CarbonImmutable */
        private readonly Closure $waitUntil,
    ) {
    }

    public function __invoke(): Generator
    {
        $waitUntil = ($this->waitUntil)();

        /** @var CarbonImmutable $now */
        $now = yield $this->getCurrentDateTime();

        $waitInterval = $now->diff($waitUntil);

        if ($waitInterval->totalMilliseconds <= 1) {
            $isTimerFired = true; // already timed-out
        } else {
            $isTimerFired = !yield Workflow::awaitWithTimeout(
                $waitInterval,
                fn (): bool => ($this->waitUntil)()->notEqualTo($waitUntil),
            );
        }

        if (!$isTimerFired) {
            return yield $this();
        }

        $this->isCompleted = true;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    private function getCurrentDateTime(): Generator
    {
        /** @var int $currentTimestamp */
        $currentTimestamp = yield Workflow::sideEffect(static fn (): int => CarbonImmutable::now()->getTimestamp());

        return CarbonImmutable::createFromTimestamp($currentTimestamp);
    }
}
