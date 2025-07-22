<?php

declare(strict_types=1);

namespace App\Playground\Temporal\IntertwinedSequence;

use Generator;
use Temporal\Activity;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Promise;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class IntertwinedSequenceWorkflow
{
    private IntertwinedSequenceActivity|Proxy $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            IntertwinedSequenceActivity::class,
            Activity\ActivityOptions::new()
                ->withScheduleToCloseTimeout(3),
        );
    }

    #[WorkflowMethod]
    public function execute(int $limit): Generator
    {
        $results = [];

        $oddNumbersCoroutine = Workflow::async(function () use ($limit, &$results) {
            for ($i = 1; $i <= $limit; $i += 2) {
                $results[] = yield $this->activity->print($i);
            }
        });

        $evenNumbersCoroutine = Workflow::async(function () use ($limit, &$results) {
            for ($i = 2; $i <= $limit; $i += 2) {
                $results[] = yield $this->activity->print($i);
            }
        });

        yield Promise::all([$evenNumbersCoroutine, $oddNumbersCoroutine]);

        // it could result in any merge of odd and even numbers

        return $results;
    }
}
