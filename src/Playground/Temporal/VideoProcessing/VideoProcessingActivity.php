<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VideoProcessing;

use Carbon\CarbonInterval;
use Exception;
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

#[ActivityInterface('VideoProcessing')]
#[AssignWorker('default')]
#[WithMonologChannel('video_processing')]
final readonly class VideoProcessingActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public static function create(): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(8)
                ->withHeartbeatTimeout(2) // try running with --beat-range=[1,3]
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::milliseconds(50))
                        ->withBackoffCoefficient(1.05),
                ),
        );
    }

    /** @param array{int,int} $beatRange */
    #[ActivityMethod]
    public function render(string $videoPath, bool $fail, array $beatRange): string
    {
        /** @var int $lastIteration */
        $lastIteration = Activity::getHeartbeatDetails(Type::TYPE_INT) ?? -1;

        for ($i = $lastIteration + 1; $i < 5; ++$i) {
            $this->logger->info(
                'Rendering iteration for video: {videoPath}, i: {iteration}',
                [
                    'videoPath' => $videoPath,
                    'iteration' => $i
                ],
            );

            sleep(1);

            $beatInterval = random_int(...$beatRange);

            if ($i % $beatInterval === 0) {
                $this->logger->info('Sending Heartbeat for iteration: {iteration}', ['iteration' => $i]);

                try {
                    Activity::heartbeat($i);
                } catch (ActivityCompletionException $e) {
                    $this->logger->error(
                        'Current activity heartbeat failed: {iteration}, {message}',
                        [
                            'message' => $e->getMessage(),
                            'iteration' => $i,
                            'exception' => $e,
                        ],
                    );

                    throw $e;
                }

                if ($fail) {
                    $this->logger->error(
                        'Failing iteration: {iteration}',
                        ['iteration' => $i],
                    );

                    // This will fail every time, but eventually it'll complete due to heartbeat.

                    throw new LogicException(sprintf("Fail iteration: %d", $i));
                }
            }
        }

        $this->logger->info("Finished rendering video: $videoPath");

        return "Rendered video from path: $videoPath";
    }
}


