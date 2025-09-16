<?php

/** @noinspection PhpVoidFunctionResultUsedInspection */

declare(strict_types=1);

namespace App\Playground\Temporal\ScheduledOrders\Workflow;

use App\Support\Temporal\Timer\ReactiveTimer;
use Carbon\CarbonImmutable;
use Generator;
use RuntimeException;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final class PlaceScheduledOrderWorkflow
{
    private ReactiveTimer $timer;

    private PlaceScheduledOrderActivity|Proxy $activity;

    #[Workflow\WorkflowInit]
    public function __construct(
        private readonly string $orderId,
        private CarbonImmutable $placementDate,
    ) {
        $this->activity = PlaceScheduledOrderActivity::create();
    }

    #[WorkflowMethod]
    public function execute(): Generator
    {
        yield ($this->timer = new ReactiveTimer(fn () => $this->placementDate))();

        yield $this->activity->placeOrder($this->orderId);
    }

    #[Workflow\UpdateMethod]
    public function changePlacementDate(CarbonImmutable $placementDate): void
    {
        if ($this->timer->isCompleted()) {
            throw new RuntimeException('Cannot change placement date after the timer has been completed.');
        }

        $this->placementDate = $placementDate;
    }
}
