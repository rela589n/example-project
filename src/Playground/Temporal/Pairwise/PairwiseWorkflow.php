<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Pairwise;

use Generator;
use Temporal\Activity;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

use function sprintf;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class PairwiseWorkflow
{
    private PairwiseActivity|Proxy $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            PairwiseActivity::class,
            Activity\ActivityOptions::new()
                ->withScheduleToCloseTimeout(3),
        );
    }

    #[WorkflowMethod]
    public function pair(): Generator
    {
        // Try to run the console command and then swap the first call with the second.
        // It will result in: ("First, First")

        /** @var string $first */
        $first = yield $this->activity->call('First');

        yield Workflow::timer(7);

        /** @var string $second */
        $second = yield $this->activity->call('Second');

        // As you can see, Replay doesn't take into account arguments
        // of an activity call (neither of the timers)

        return sprintf('%s, %s', $first, $second);
    }
}
