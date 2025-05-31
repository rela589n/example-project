<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VideoProcessing;

use Generator;
use Temporal\Activity;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class VideoProcessingWorkflow
{
    private VideoProcessingActivity|Proxy $activities;

    public function __construct()
    {
        $this->activities = VideoProcessingActivity::create();
    }

    /** @param array{int,int} $beatRange */
    #[WorkflowMethod]
    public function process(string $videoPath, bool $fail, array $beatRange): Generator
    {
        return yield $this->activities->render($videoPath, $fail, $beatRange);
    }
}

