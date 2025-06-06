<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

declare(strict_types=1);

namespace App\Playground\Temporal\ScheduledOrders\Workflow;

use App\Playground\Temporal\ScheduledOrders\WaitUntil;
use Carbon\CarbonImmutable;
use Generator;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final class PlaceScheduledOrderWorkflow
{
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
        yield new WaitUntil(fn () => $this->placementDate)();

        yield $this->activity->placeOrder($this->orderId);
    }

    #[Workflow\UpdateMethod]
    public function changePlacementDate(CarbonImmutable $placeAt): void
    {
        $this->placementDate = $placeAt;
    }
}
