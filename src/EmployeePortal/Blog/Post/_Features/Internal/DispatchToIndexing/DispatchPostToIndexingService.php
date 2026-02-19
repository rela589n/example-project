<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\Internal\DispatchToIndexing;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler('default.bus')]
final readonly class DispatchPostToIndexingService
{
    public function __invoke(DispatchPostToIndexingCommand $command): void
    {
        $command->execute($this);
    }
}
