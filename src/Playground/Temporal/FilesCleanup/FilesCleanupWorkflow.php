<?php

declare(strict_types=1);

namespace App\Playground\Temporal\FilesCleanup;

use Carbon\CarbonInterval;
use Generator;
use Temporal\Activity\ActivityOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class FilesCleanupWorkflow
{
    public const TYPE = 'FilesCleanupWorkflow';

    private FilesCleanupActivity|Proxy $activity;

    public function __construct()
    {
        $this->activity = Workflow::newActivityStub(
            FilesCleanupActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::hour())
                ->withHeartbeatTimeout(CarbonInterval::minute()),
        );
    }

    #[Workflow\WorkflowMethod]
    public function execute(): Generator
    {
        yield $this->activity->execute(); // @phpstan-ignore method.void
    }
}
