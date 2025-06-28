<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Schema;

use App\Support\Partitioning\Entity\PartitionedEntityInterface;
use App\Support\Partitioning\Entity\PartitionId;
use App\Support\Partitioning\Manager\PartitionManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preRemove)]
final class PartitionedEntityTableSchemaSetupListener
{
    /** @var array<class-string<PartitionedEntityInterface>,array<int|string,PartitionId>> */
    private array $scheduledPartitionSetups = [];

    public function __construct(
        private readonly PartitionManagerRegistry $partitionManagerRegistry,
    ) {
    }

    /** @api */
    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof PartitionedEntityInterface) {
            return;
        }

        $partitionId = $entity->getPartitionId();

        $this->scheduledPartitionSetups[$entity::class][$partitionId->getId()] = $partitionId;

        $eventArgs->getObjectManager()->getEventManager()->addEventListener(Events::onFlush, $this);
    }

    /** @api */
    public function preRemove(PreRemoveEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof PartitionedEntityInterface) {
            return;
        }
        $partitionId = $entity->getPartitionId();

        unset($this->scheduledPartitionSetups[$entity::class][$partitionId->getId()]);
    }

    /** @api */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();

        $this->setupPartitions($this->partitionManagerRegistry->getManager($entityManager)->schemaManager);

        $entityManager->getEventManager()->removeEventListener(Events::onFlush, $this);
    }

    private function setupPartitions(PartitionSchemaManager $schemaManager): void
    {
        $scheduledPartitionSetups = $this->scheduledPartitionSetups;
        $this->scheduledPartitionSetups = [];

        foreach ($scheduledPartitionSetups as $entityClassName => $partitionIds) {
            foreach ($partitionIds as $partitionId) {
                $schemaManager->createPartitionTable($entityClassName, $partitionId);
            }
        }
    }
}
