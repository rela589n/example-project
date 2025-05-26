<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use Generator;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class QuoteOfTheDayWorkflow
{
    private QuoteOfTheDayActivity|Proxy $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            QuoteOfTheDayActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(2),
        );
    }

    #[WorkflowMethod]
    public function getQuoteOfTheDay(int $dayIndex): Generator
    {
        return yield $this->activity->getQuoteOfTheDay($dayIndex);
    }
}
