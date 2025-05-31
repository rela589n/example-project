<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Ack;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\Exception\Client\ActivityCanceledException;
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
                ->withStartToCloseTimeout(8),
        );
    }

    #[ActivityMethod]
    public function acknowledge(string|AcknowledgeSignatureCommand $arg): void
    {
        if (is_string($arg)) {
            $command = new AcknowledgeSignatureCommand($arg, null);
        } else {
            $command = $arg;
        }

        $this->logger->info(
            'Acknowledging document: {documentId}, path: {path}',
            ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
        );

        // like http request
        sleep(5);

        // this will fail if the workflow was cancelled
        try {
            Activity::heartbeat('');
        } catch (ActivityCanceledException $e) {
            $this->logger->info(
                'Document acknowledgement was cancelled: {documentId}, path: {path}',
                ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
            );

            throw $e;
        }

        $this->logger->info(
            'Document acknowledged: {documentId}, path: {path}',
            ['documentId' => $command->documentId, 'path' => $command->signedFilePath],
        );
    }

    #[ActivityMethod]
    public function cancel(AcknowledgeSignatureCommand $command): void
    {
        $this->logger->info(
            'Document {documentId}, path {path} acknowledgement cancelled',
            ['documentId' => $command, 'path' => $command->signedFilePath],
        );
    }
}
