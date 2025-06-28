<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Resolve;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::CONTROLLER_ARGUMENTS, priority: -1000)] // executed last
final readonly class ResolvePartitionControllerListener
{
    public function __construct(
        private PartitionIdResolver $partitionIdResolver,
        private PartitionedEntitiesMiddleware $middleware,
    ) {
    }

    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $partitionId = $this->partitionIdResolver->resolve();
        $controller = $event->getController();

        if (null === $partitionId) {
            return;
        }

        $event->setController(fn (mixed ...$args): mixed => $this->middleware->__invoke($partitionId, $controller, ...$args));
    }
}
