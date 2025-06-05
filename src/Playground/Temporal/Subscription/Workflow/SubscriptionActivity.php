<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Subscription\Workflow;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\NilUuid;
use Symfony\Component\Uid\Uuid;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('Subscription.')]
#[AssignWorker('default')]
#[WithMonologChannel('subscription')]
final readonly class SubscriptionActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            Activity\ActivityOptions::new()
                ->withStartToCloseTimeout(5),
        );
    }

    #[Activity\ActivityMethod]
    public function sendWelcomeEmail(string $userId): void
    {
        $this->logger->info('Sending welcome email to {userId}', [
            'userId' => $userId,
        ]);
    }

    #[Activity\ActivityMethod]
    public function chargePayment(string $userId, string $paymentId): void
    {
        $this->logger->info('Charging payment {paymentId} for {userId}', [
            'userId' => $userId,
            'paymentId' => $paymentId,
        ]);

        if (Uuid::fromString($userId)->equals(new NilUuid())) {
            throw new ApplicationFailure('Payment failed', 'payment_failed', true);
        }
    }

    #[Activity\ActivityMethod]
    public function extendSubscriptionUntil(string $userId, DateTimeImmutable $endDate): void
    {
        $this->logger->info('Extending subscription for {userId} until {endDate}',
            [
                'userId' => $userId,
                'endDate' => $endDate->format('Y-m-d H:i:s'),
            ],
        );
    }

    #[Activity\ActivityMethod]
    public function sendEndOfTrialPeriodNotification(string $userId): void
    {
        $this->logger->info('Sending end of trial period notification to {userId}', [
            'userId' => $userId,
        ]);
    }

    public function sendSorryToSeeYouGoEmail(string $userId, CarbonImmutable $validUntil): void
    {
        if ($validUntil->addMinutes(20)->lte(CarbonImmutable::now())) {
            $stillValid = true;
        }

        $this->logger->info('Sending sorry to see you go email to {userId} with valid ({isValid}) until {validUntil}', [
            'userId' => $userId,
            'isValid' => var_export(isset($stillValid), true),
            'validUntil' => $validUntil->format('Y-m-d H:i:s'),
        ]);
    }

    public function cancelPayment(string $userId, string $paymentId): void
    {
        $this->logger->info('Cancelling payment {paymentId} for user {userId}', [
            'userId' => $userId,
            'paymentId' => $paymentId,
        ]);
    }

    public function sendPaymentFailureEmail(string $userId): void
    {
        $this->logger->info('Sending payment failure email to {userId}', [
            'userId' => $userId,
        ]);
    }
}
