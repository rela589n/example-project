<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\Internal\DispatchToIndexing;

use Symfony\Component\Uid\Uuid;

final readonly class DispatchPostToIndexingCommand
{
    public function __construct(
        private Uuid $postId,
    ) {
    }

    public function execute(DispatchPostToIndexingService $service): void
    {
        // TODO: send a command to a Chatbot service
    }
}
