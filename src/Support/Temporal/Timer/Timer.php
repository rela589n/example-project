<?php

declare(strict_types=1);

namespace App\Support\Temporal\Timer;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Generator;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Workflow;
use Temporal\Workflow\CancellationScopeInterface;

final class Timer
{
    private CancellationScopeInterface $task;

    private TimerState $state;

    public function __construct(
        private CarbonImmutable $fireAt,
    ) {
        $this->schedule($fireAt);
    }

    public function __invoke(): Generator
    {
        /** @noinspection PhpFieldImmediatelyRewrittenInspection */
        $this->state = TimerState::STARTED;

        try {
            yield $this->task;
        } catch (CanceledFailure $e) {
            if ($this->isCancelled()) {
                return null; // the timer was cancelled normally, no need to throw
            }

            if ($this->isScheduled()) {
                return yield $this(); // the timer was reset and is scheduled again
            }

            throw $e; // the timer was cancelled due to an external cancellation request
        }

        $this->state = TimerState::FIRED;
    }

    public function rewind(CarbonImmutable $fireAt): void
    {
        if ($this->fireAt->eq($fireAt)) {
            return;
        }

        $this->cancel();
        $this->schedule($fireAt);
    }

    public function cancel(): void
    {
        $this->task->cancel();
        $this->state = TimerState::CANCELLED;
    }

    public function isScheduled(): bool
    {
        return TimerState::SCHEDULED === $this->state;
    }

    public function isStarted(): bool
    {
        return TimerState::STARTED === $this->state;
    }

    public function isCompleted(): bool
    {
        return $this->isFired() || $this->isCancelled();
    }

    public function isFired(): bool
    {
        return TimerState::FIRED === $this->state;
    }

    public function isCancelled(): bool
    {
        return TimerState::CANCELLED === $this->state;
    }

    private function schedule(CarbonImmutable $fireAt): void
    {
        $this->fireAt = $fireAt;
        $this->task = $this->createTimerTask($fireAt);
        $this->state = TimerState::SCHEDULED;
    }

    private function createTimerTask(DateTimeImmutable $fireAt): CancellationScopeInterface
    {
        return Workflow::async(function () use ($fireAt) {
            /** @var CarbonImmutable $now */
            $now = yield $this->getCurrentDateTime();

            $interval = $now->diff($fireAt);

            if ($interval->totalMilliseconds <= 1) {
                return; // already timed-out
            }

            yield Workflow::timer($interval);
        });
    }

    private function getCurrentDateTime(): Generator
    {
        /** @var int $currentTimestamp */
        $currentTimestamp = yield Workflow::sideEffect(static fn (): int => CarbonImmutable::now()->getTimestamp());

        return CarbonImmutable::createFromTimestamp($currentTimestamp);
    }
}
