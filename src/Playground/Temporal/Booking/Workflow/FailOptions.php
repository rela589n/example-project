<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Booking\Workflow;

use Temporal\Activity\ActivityInfo;

use function random_int;

final readonly class FailOptions
{
    public function __construct(
        private(set) BookFailFlag $flag,
        private(set) int $attempts,
    ) {
    }

    public static function doNotFail(): self
    {
        return new self(BookFailFlag::NONE, 0);
    }

    public static function failAt(BookFailFlag $flag, int $attempt): self
    {
        return new self($flag, $attempt);
    }

    public function shouldFail(BookFailFlag $flag, ActivityInfo $activityInfo): bool
    {
        if ($flag === $this->flag) {
            return true;
        }

        if (BookFailFlag::RANDOM !== $this->flag) {
            return false;
        }

        // Random

        if ($activityInfo->attempt > $this->attempts) {
            return false;
        }

        // since $activityInfo->attempt is increasing,
        // there's a bigger chance to not fail on the next retries
        // (actually, if the max retries number is less than $this->attempts, it'll always be succeeded)
        return random_int($activityInfo->attempt, $this->attempts) !== $this->attempts;
    }
}
