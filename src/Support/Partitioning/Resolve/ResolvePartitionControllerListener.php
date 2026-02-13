<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Resolve;

use App\Support\Partitioning\Entity\PartitionId;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::CONTROLLER_ARGUMENTS, priority: -1000)] // executed last
final readonly class ResolvePartitionControllerListener
{
    public function __construct(
        /** @var iterable<PartitionIdResolver> */
        #[AutowireIterator(PartitionIdResolver::class)]
        private iterable $partitionIdResolvers,
        private PartitionedEntitiesMiddleware $middleware,
    ) {
    }

    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();

        if ([] === $partitionIds = $this->resolvePartitionIds()) {
            return;
        }

        $event->setController(fn (mixed ...$args): mixed => $this->middleware->__invoke(
            $partitionIds,
            static fn (): mixed => $controller(...$args),
        ));
    }

    /** @return list<PartitionId> */
    private function resolvePartitionIds(): array
    {
        $partitionIds = [];

        foreach ($this->partitionIdResolvers as $partitionIdResolver) {
            if (null !== $partitionId = $partitionIdResolver->resolve()) {
                $partitionIds[] = $partitionId;
            }
        }

        return $partitionIds;
    }
}
