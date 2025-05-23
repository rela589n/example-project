<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use Generator;
use React\Promise\Promise;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
final readonly class QuoteOfTheDayWorkflow
{
    private QuoteOfTheDayActivity|ActivityProxy $activity;

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
