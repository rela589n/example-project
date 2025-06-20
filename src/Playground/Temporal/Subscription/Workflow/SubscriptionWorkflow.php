<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription\Workflow;

use App\Support\Temporal\Timer\Timer;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Exception;
use Generator;
use Symfony\Component\Uid\Uuid;
use Temporal\Exception\Failure\CanceledFailure;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInit;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;
use Webmozart\Assert\Assert;

#[WorkflowInterface]
#[AssignWorker('default')]
final class SubscriptionWorkflow
{
    private ?CarbonImmutable $subscriptionValidUntil;

    private readonly CarbonInterval $recurrence;

    /** Interval, by which the end-of-trial notification will be sent before the paid subscription starts */
    private readonly CarbonInterval $endOfTrialNotificationOffset;

    /** Interval, by which the next cycle workflow will run ahead of time */
    private readonly CarbonInterval $nextCycleRunPreemptionOffset;

    private readonly SubscriptionActivity|Proxy $activities;

    #[WorkflowInit]
    public function __construct(
        private readonly SubscriptionStatus $subscriptionStatus,
    ) {
        $this->subscriptionValidUntil = $this->subscriptionStatus->cycleRenewalDate;
        $this->recurrence = CarbonInterval::seconds(40);
        $this->endOfTrialNotificationOffset = CarbonInterval::seconds(20);
        $this->nextCycleRunPreemptionOffset = CarbonInterval::seconds(10);
        $this->activities = SubscriptionActivity::create();
    }

    #[WorkflowMethod]
    public function subscription(): Generator
    {
        try {
            yield $this->waitUntil($this->subscriptionStatus->cycleRenewalDate?->sub($this->nextCycleRunPreemptionOffset));

            /** @var CarbonImmutable $nextCycleDate */
            $nextCycleDate = yield $this->processSubscriptionCycle();
        } catch (CanceledFailure $e) {
            if (isset($this->subscriptionValidUntil)) {
                yield Workflow::asyncDetached(
                    fn () => yield $this->activities->sendSorryToSeeYouGoEmail($this->subscriptionStatus->userId, $this->subscriptionValidUntil)
                );
            }

            throw $e;
        }

        // Starting workflow for the next cycle
        return Workflow::continueAsNew(
            Workflow::getInfo()->type->name,
            [$this->subscriptionStatus->nextCycle($nextCycleDate)],
        );
    }

    private function processSubscriptionCycle(): Generator
    {
        if ($this->subscriptionStatus->isTrialPeriod) {
            return $this->processTrialPeriod();
        }

        return $this->processNormalCycle();
    }

    /** @noinspection PhpRedundantVariableDocTypeInspection */
    private function processTrialPeriod(): Generator
    {
        /** @var CarbonImmutable $startingCycleDate */
        $startingCycleDate = $this->subscriptionStatus->cycleRenewalDate ?? yield $this->getCurrentDate();

        /** @var CarbonImmutable $nextCycleDate */
        $nextCycleDate = yield $this->extendSubscriptionUntilNextCycle($startingCycleDate);

        yield $this->activities->sendWelcomeEmail($this->subscriptionStatus->userId);

        // Sending an end-of-trial email the day before the paid subscription begins.
        yield $this->waitUntil($nextCycleDate->sub($this->endOfTrialNotificationOffset));

        yield $this->activities->sendEndOfTrialPeriodNotification($this->subscriptionStatus->userId);

        return $nextCycleDate;
    }

    private function processNormalCycle(): Generator
    {
        $currentCycleDate = $this->subscriptionStatus->cycleRenewalDate;

        Assert::notNull($currentCycleDate);

        yield $this->chargePayment();

        return yield $this->extendSubscriptionUntilNextCycle($currentCycleDate);
    }

    private function chargePayment(): Generator
    {
        /** @var string $paymentId */
        $paymentId = yield Workflow::sideEffect(static fn (): string => Uuid::v7()->toRfc4122());

        try {
            yield $this->activities->chargePayment($this->subscriptionStatus->userId, $paymentId);
        } catch (Exception $e) {
            $saga = new Workflow\Saga()->setParallelCompensation(true);

            $saga->addCompensation(
                fn () => yield $this->activities->cancelPayment($this->subscriptionStatus->userId, $paymentId),
            );
            $saga->addCompensation(
                fn () => yield $this->activities->sendPaymentFailureEmail($this->subscriptionStatus->userId),
            );

            yield $saga->compensate();

            throw $e;
        }
    }

    private function extendSubscriptionUntilNextCycle(CarbonImmutable $currentCycleDate): Generator
    {
        /** @var CarbonImmutable $nextCycleDate */
        $nextCycleDate = yield $this->getNextCycleDate($currentCycleDate);

        yield $this->activities->extendSubscriptionUntil($this->subscriptionStatus->userId, $nextCycleDate);

        $this->subscriptionValidUntil = $nextCycleDate;

        return $nextCycleDate;
    }

    private function getCurrentDate(): Generator
    {
        /** @var int $currentTimestamp */
        $currentTimestamp = yield Workflow::sideEffect(static fn (): int => CarbonImmutable::now()->getTimestamp());

        return CarbonImmutable::createFromTimestamp($currentTimestamp);
    }

    private function getNextCycleDate(CarbonImmutable $currentCycleDate): Generator
    {
        /** @var int $nextCycleTimestamp */
        $nextCycleTimestamp = yield Workflow::sideEffect(fn (): int => $currentCycleDate->add($this->recurrence)->getTimestamp());

        return CarbonImmutable::createFromTimestamp($nextCycleTimestamp);
    }

    private function waitUntil(?CarbonImmutable $timestamp): Generator
    {
        if (null === $timestamp) {
            return;
        }

        yield new Timer($timestamp)();
    }
}
