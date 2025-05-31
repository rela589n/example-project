<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow;

use Temporal\Activity\ActivityInfo;

final readonly class FailOptions
{
    public function __construct(
        private(set) FailFlag $flag,
        private(set) int $attempts,
    ) {
    }

    public static function doNotFail(): self
    {
        return new self(FailFlag::NONE, 0);
    }

    public static function failAt(FailFlag $flag, int $attempt): self
    {
        return new self($flag, $attempt);
    }

    public function shouldFail(FailFlag $flag, ActivityInfo $activityInfo): bool
    {
        if ($flag === $this->flag) {
            return true;
        }

        if (FailFlag::RANDOM !== $this->flag) {
            return false;
        }

        if ($activityInfo->attempt > $this->attempts) {
            return false;
        }

        // since $activityInfo->attempt is increasing,
        // there's a bigger chance to not fail on the next retries
        // (actually, if the max retries number is less than $this->attempts, it'll always be succeeded)
        return random_int($activityInfo->attempt, $this->attempts) !== $this->attempts;
    }
}
