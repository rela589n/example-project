<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VideoProcessing;

use Carbon\CarbonInterval;
use LogicException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\DataConverter\Type;
use Temporal\Exception\Client\ActivityCompletionException;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

use function random_int;
use function sleep;
use function sprintf;

#[ActivityInterface('VideoProcessing.')]
#[AssignWorker('default')]
#[WithMonologChannel('video_processing')]
final readonly class VideoProcessingActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(int $length): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                ->withScheduleToStartTimeout(CarbonInterval::seconds(5))
                ->withScheduleToCloseTimeout(CarbonInterval::seconds($length * 1.5))
                ->withHeartbeatTimeout(2) // try running with --beat-range=[1,3] to see timeout fails
                // this will ensure that activity is cancelled before throwing CanceledFailure to the Workflow
                ->withCancellationType(Activity\ActivityCancellationType::WaitCancellationCompleted)
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::milliseconds(50))
                        ->withBackoffCoefficient(1.05),
                ),
        );
    }

    /** @param array{int,int} $beatRange */
    #[ActivityMethod]
    public function render(int $length, array $beatRange, ?int $beatLimit, bool $failAfterHeartbeat): string
    {
        $beatLimit ??= $length;

        /** @var int $lastIteration */
        $lastIteration = Activity::getHeartbeatDetails(Type::TYPE_INT) ?? -1;

        for ($i = $lastIteration + 1; $i < $length; ++$i) {
            $this->logger->info(
                "Rendering Iteration:\t{iteration}",
                ['iteration' => $i],
            );

            sleep(1);

            $beatInterval = random_int(...$beatRange);

            if ($i % $beatInterval === 0) {
                try {
                    if ($i < $beatLimit) {
                        $this->logger->info("Sending Heartbeat:\t{iteration}", ['iteration' => $i]);

                        Activity::heartbeat($i);
                    } else {
                        $this->logger->info("Heartbeat stopped:\t{iteration}", ['iteration' => $i]);
                    }
                } catch (ActivityCompletionException $e) {
                    $this->logger->error(
                        "Heartbeat failed:\t{iteration}, {message}",
                        [
                            'iteration' => $i,
                            'message' => $e->getMessage(),
                            'exception' => $e,
                        ],
                    );

                    // Workflow will have to wait for this Activity to exit before throwing CanceledFailure

                    sleep(1);

                    // if previous sleep was longer than heartbeat timeout,
                    // at this point, workflow execution will have already been cancelled.
                    $this->logger->error(
                        "Activity exiting:\t{iteration}",
                        ['iteration' => $i],
                    );

                    throw $e;
                }

                if ($failAfterHeartbeat) {
                    $this->logger->error(
                        'Failing iteration: {iteration}',
                        ['iteration' => $i],
                    );

                    // This will fail every time, but eventually it'll complete due to heartbeat.

                    throw new LogicException(sprintf('Fail iteration: %d', $i));
                }
            }
        }

        $this->logger->info("Finished rendering video: {$length}");

        return "Rendered video: {$length}";
    }

    #[ActivityMethod]
    public function cancel(int $length): void
    {
        $this->logger->info("Cancelling video processing:\t{length}", ['length' => $length]);

        // if compensation takes longer than main activity options restrict,
        // it will fail just like any other activity can fail
        // sleep(5);

        $this->logger->warning("Cancelled video processing:\t{length}", ['length' => $length]);
    }
}
