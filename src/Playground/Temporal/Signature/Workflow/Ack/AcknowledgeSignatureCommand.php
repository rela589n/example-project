<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Ack;

final readonly class AcknowledgeSignatureCommand
{
    public function __construct(
        private(set) string $documentId,
        private(set) ?string $signedFilePath,
        private(set) SignFailFlag $failFlag,
    ) {
    }
}
