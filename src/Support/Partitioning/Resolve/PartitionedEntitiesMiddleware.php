<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Resolve;

use App\Support\Partitioning\Entity\PartitionId;
use App\Support\Partitioning\Manager\PartitionManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final readonly class PartitionedEntitiesMiddleware
{
    public function __construct(
        private PartitionManagerRegistry $partitionManagerRegistry,
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function __invoke(PartitionId $partitionId, callable $next, ...$args): mixed
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->managerRegistry->getManager();

        $partitionManager = $this->partitionManagerRegistry->getManager($entityManager);

        return $partitionManager->callWithPartition($partitionId, static fn (): mixed => $next(...$args));
    }
}
