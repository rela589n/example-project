<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Manager;

use App\Support\Partitioning\Entity\PartitionedEntityInterface;
use App\Support\Partitioning\Entity\PartitionId;
use App\Support\Partitioning\Schema\PartitionSchemaManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;

final class PartitionManager
{
    private ClassMetadataFactory $metadataFactory {
        get => $this->metadataFactory ??= $this->entityManager->getMetadataFactory();
    }

    public PartitionSchemaManager $schemaManager {
        get => new PartitionSchemaManager($this, $this->entityManager->getConnection());
    }

    /** @var array<class-string<PartitionedEntityInterface>,string> */
    public array $tableMapping {
        get {
            if (isset($this->tableMapping)) {
                return $this->tableMapping;
            }

            $partitionedEntityNames = $this->getPartitionedEntityClassNames();

            /** @var ClassMetadata[] $originalMetadata */
            $originalMetadata = array_map(
                $this->metadataFactory->getMetadataFor(...),
                $partitionedEntityNames,
            );
            $tableNames = array_map(
                static fn (ClassMetadata $metadata): string => $metadata->getTableName(),
                $originalMetadata,
            );

            return $this->tableMapping = array_combine($partitionedEntityNames, $tableNames);
        }
    }

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /** @param callable(): mixed $callable */
    public function callWithPartition(PartitionId $partitionId, callable $callable): mixed
    {
        $this->setUpPartitionMappings($partitionId);

        try {
            return $callable();
        } finally {
            $this->tearDownPartitionMappings();
        }
    }

    private function setUpPartitionMappings(PartitionId $partitionId): void
    {
        foreach ($this->tableMapping as $entityClassName => $tableName) {
            $this->setUpPartitionMapping($entityClassName, $partitionId);
        }
    }

    /** @param class-string<PartitionedEntityInterface> $entityClassName */
    private function setUpPartitionMapping(string $entityClassName, PartitionId $partitionId): void
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataFor($entityClassName);

        $partitionTableName = $partitionId->getTableName($this->tableMapping[$entityClassName]);

        $classMetadata->setPrimaryTable(['name' => $partitionTableName] + $classMetadata->table);
    }

    private function tearDownPartitionMappings(): void
    {
        foreach ($this->tableMapping as $entityClassName => $tableName) {
            $this->tearDownPartitionMapping($entityClassName);
        }
    }

    /** @param class-string<PartitionedEntityInterface> $entityClassName */
    private function tearDownPartitionMapping(string $entityClassName): void
    {
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $this->metadataFactory->getMetadataFor($entityClassName);

        $tableName = $this->tableMapping[$entityClassName];

        $classMetadata->setPrimaryTable(['name' => $tableName] + $classMetadata->table);
    }

    /** @return class-string<PartitionedEntityInterface>[] */
    private function getPartitionedEntityClassNames(): array
    {
        /** @var class-string<PartitionedEntityInterface>[] $partitionedEntityNames */
        $partitionedEntityNames = [];

        /** @var ClassMetadata $metadata */
        foreach ($this->metadataFactory->getAllMetadata() as $metadata) {
            if (!is_a($metadata->getName(), PartitionedEntityInterface::class, true)) {
                continue;
            }

            $partitionedEntityNames[] = $metadata->getName();
        }

        return $partitionedEntityNames;
    }
}
