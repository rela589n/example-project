<?php

declare(strict_types=1);

namespace App\Playground\Temporal\FilesCleanup;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('FilesCleanup.')]
#[AssignWorker('default')]
#[WithMonologChannel('files_cleanup')]
final readonly class FilesCleanupActivity
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[ActivityMethod]
    public function execute(): void
    {
        $this->logger->info('Files cleanup activity started.');

        // Here you would implement the logic to clean up files.
        // For example, deleting temporary files or old logs.

        $this->logger->info('Files cleanup activity completed successfully.');
    }
}
