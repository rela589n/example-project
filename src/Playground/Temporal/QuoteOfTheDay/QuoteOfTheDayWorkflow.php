<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use React\Promise\Promise;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\ActivityProxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
final readonly class QuoteOfTheDayWorkflow
{
    /** @var ActivityProxy&QuoteOfTheDayActivity */
    private ActivityProxy $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            QuoteOfTheDayActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(2),
        );
    }

    #[WorkflowMethod]
    public function getQuoteOfTheDay(int $dayIndex): Promise
    {
        return $this->activity->getQuoteOfTheDay($dayIndex);
    }
}
