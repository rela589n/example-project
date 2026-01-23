<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\Create\Outbox;

use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Features\Internal\DispatchToIndexing\DispatchPostToIndexingCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler('event.bus')]
final readonly class DispatchToIndexingOutbox
{
    public function __construct(
        #[Autowire('@default.bus')]
        private MessageBusInterface $defaultBus,
    ) {
    }

    public function __invoke(PostCreatedEvent $event): void
    {
        $command = new DispatchPostToIndexingCommand($event->getId());

        $this->defaultBus->dispatch($command);
    }
}
