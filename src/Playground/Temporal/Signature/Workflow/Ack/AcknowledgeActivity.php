<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Ack;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Exception\Client\ActivityCanceledException;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('AcknowledgeActivity.')]
#[AssignWorker('default')]
#[WithMonologChannel('signature')]
final readonly class AcknowledgeActivity
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
                // backoff: 1 + 3 + 9 < timeout
                ->withScheduleToStartTimeout(5)
                ->withScheduleToCloseTimeout(10)
                ->withRetryOptions(
                    RetryOptions::new()
                        //  ->withNonRetryableExceptions([
                        //      // ServerExceptionInterface::class, // - interface won't work here
                        //      ServerException::class, // this is matched by previous: as well
                        //  ])
                        ->withBackoffCoefficient(3),
                ),
        );
    }

    #[ActivityMethod]
    public function acknowledge(string|AcknowledgeSignatureCommand $arg): void
    {
        if (is_string($arg)) {
            $command = new AcknowledgeSignatureCommand($arg, null, SignFailFlag::NONE);
        } else {
            $command = $arg;
        }

        $this->logger->info(
            'Acknowledging document: {documentId}, path: {path}',
            ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
        );

        // like http request
        sleep(4);

        try {
            // this will fail if the workflow was cancelled/terminated
            Activity::heartbeat('');

            match ($command->failFlag) {
                SignFailFlag::ACK_TIMEOUT_WITHIN_LIMIT => throw new TimeoutException('Http timeout reached'),
                SignFailFlag::ACK_SERVER_ERROR => throw new ServerException(new MockResponse('Internal server error', ['http_code' => 500])),
                // if the activity times-out by execution timeout, but is still doing something,
                // it's very bad, because compensations will kick in before it completes, and
                // it might complete after compensation, resulting in a corrupted state
                // Setting cancellationType=WaitCancellationCompleted won't help, since it's already timed-out
                SignFailFlag::ACK_TIMEOUT => sleep(7),
                default => null,
            };
        } catch (ActivityCanceledException $e) {
            $this->logger->info(
                'Document acknowledgement was cancelled: {documentId}, path: {path}',
                ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
            );

            throw $e;
        } catch (ServerException $e) {
            // allow 1 retry for ServerException
            if (Activity::getInfo()->attempt === 1) {
                throw $e;
            }

            throw new ApplicationFailure('APP Server error', $e::class, true, previous: $e);
        }

        $this->logger->info(
            'Document acknowledged: {documentId}, path: {path}',
            ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
        );
    }

    #[ActivityMethod]
    public function cancel(AcknowledgeSignatureCommand $command): void
    {
        $this->logger->warning(
            'Document {documentId}, path {path} acknowledgement cancelled',
            ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
        );
    }
}
