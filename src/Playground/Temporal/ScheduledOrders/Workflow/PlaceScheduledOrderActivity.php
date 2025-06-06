<?php

declare(strict_types=1);

namespace App\Playground\Temporal\ScheduledOrders\Workflow;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('PlaceScheduledOrder.')]
#[AssignWorker('default')]
#[WithMonologChannel('scheduled_orders')]
final readonly class PlaceScheduledOrderActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(3),
        );
    }

    public function placeOrder(string $orderId): void
    {
        $this->logger->info('Placing order {orderId}', ['orderId' => $orderId]);
    }
}
