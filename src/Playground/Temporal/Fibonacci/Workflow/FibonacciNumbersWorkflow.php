<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Fibonacci\Workflow;

use Carbon\CarbonInterval;
use Generator;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[Workflow\WorkflowInterface]
#[AssignWorker('default')]
final class FibonacciNumbersWorkflow
{
    private FibonacciNumbersActivity|Proxy $activity;

    private int $iteration;

    #[Workflow\WorkflowInit]
    public function __construct(
        private int $limit,
    ) {
        $this->activity = Workflow::newActivityStub(
            FibonacciNumbersActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(1),
        );
    }

    #[Workflow\WorkflowMethod]
    public function run(): Generator
    {
        $a = 0;
        $b = 1;

        // Even though shiftLimit() might be called,
        // those iterations that have already been passed will be replayed again
        // up to the last processed, and only then is shiftLimit() update applied.
        for ($this->iteration = 1; $this->iteration <= $this->limit; ++$this->iteration) {
            yield $this->activity->log($this->iteration, $b);

            // You can remove this timer not to be triggered in teh next iterations by setting maxSupported: 0
            /** @var int $version */
            $version = yield Workflow::getVersion('timer-'.$this->iteration, Workflow::DEFAULT_VERSION, Workflow::DEFAULT_VERSION);

            if (Workflow::DEFAULT_VERSION === $version) {
                yield Workflow::timer(CarbonInterval::second());
            }

            [$a, $b] = [$b, $a + $b];
        }
    }

    #[Workflow\UpdateMethod]
    public function shiftLimit(int $newLimit): void
    {
        $this->limit = $newLimit;
    }

    #[Workflow\QueryMethod]
    public function getIteration(): int
    {
        return $this->iteration;
    }
}
