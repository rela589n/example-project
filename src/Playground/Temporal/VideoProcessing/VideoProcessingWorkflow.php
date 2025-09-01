<?php

/** @noinspection PhpVoidFunctionResultUsedInspection */

declare(strict_types=1);

namespace App\Playground\Temporal\VideoProcessing;

use Exception;
use Generator;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class VideoProcessingWorkflow
{
    private VideoProcessingActivity|Proxy $videoProcessing;

    #[Workflow\WorkflowInit]
    public function __construct(int $length)
    {
        $this->videoProcessing = VideoProcessingActivity::create($length);
    }

    /** @param array{int,int} $beatRange */
    #[WorkflowMethod]
    public function process(int $length, array $beatRange, ?int $beatLimit, bool $failAfterHeartBeat): Generator
    {
        try {
            return yield $this->videoProcessing->render($length, $beatRange, $beatLimit, $failAfterHeartBeat);
        } catch (Exception $e) {
            // since Activity uses cancellationType=WaitCancellationCompleted,
            // it will have already been cancelled (or timed-out by heartbeat) by this moment
            yield Workflow::asyncDetached(fn () => yield $this->videoProcessing->cancel($length));

            throw $e;
        }
    }
}
