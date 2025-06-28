<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use WeakMap;

#[AutoconfigureTag('kernel.reset', ['method' => 'reset'])]
final class PartitionManagerRegistry
{
    /** @var WeakMap<EntityManagerInterface, PartitionManager> */
    private WeakMap $partitionManagers;

    public function __construct()
    {
        $this->partitionManagers = new WeakMap();
    }

    public function getManager(EntityManagerInterface $entityManager): PartitionManager
    {
        return $this->partitionManagers[$entityManager] ??= new PartitionManager($entityManager);
    }

    public function reset(): void
    {
        $this->partitionManagers = new WeakMap();
    }
}
