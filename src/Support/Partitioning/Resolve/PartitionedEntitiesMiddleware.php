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

    /**
     * @param PartitionId[] $partitionIds
     * @param callable(): mixed $next
     */
    public function __invoke(array $partitionIds, callable $next): mixed
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->managerRegistry->getManager();

        $partitionManager = $this->partitionManagerRegistry->getManager($entityManager);

        foreach ($partitionIds as $partitionId) {
            $next = static fn (): mixed => $partitionManager->callWithPartition($partitionId, $next);
        }

        return $next();
    }
}
