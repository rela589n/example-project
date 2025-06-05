<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription\Workflow;

use Carbon\CarbonImmutable;
use Webmozart\Assert\Assert;

final readonly class SubscriptionStatus
{
    public function __construct(
        private(set) string $userId,
        private(set) bool $isTrialPeriod,
        private(set) ?CarbonImmutable $cycleRenewalDate,
    ) {
        if (!$this->isTrialPeriod) {
            Assert::notNull($this->cycleRenewalDate);
        }
    }

    public static function start(string $userId): self
    {
        return new self($userId, true, null);
    }

    public function nextCycle(CarbonImmutable $nextCycleDate): SubscriptionStatus
    {
        return new self($this->userId, false, $nextCycleDate);
    }
}
