<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Schema;

use App\Support\Partitioning\Entity\PartitionedEntityInterface;
use App\Support\Partitioning\Entity\PartitionId;
use App\Support\Partitioning\Manager\PartitionManager;
use Doctrine\DBAL\Connection;

final readonly class PartitionSchemaManager
{
    public function __construct(
        private PartitionManager $partitionManager,
        private Connection $connection,
    ) {
    }

    /** @param class-string<PartitionedEntityInterface> $entityClassName */
    public function createPartitionTable(string $entityClassName, PartitionId $partitionId): void
    {
        $mainTableName = $this->partitionManager->tableMapping[$entityClassName];

        $sql = sprintf(
            <<<'SQL'
                CREATE TABLE IF NOT EXISTS
                    %s
                    PARTITION OF %s
                        FOR VALUES IN (%s)
                SQL,
            $partitionId->getTableName($mainTableName),
            $mainTableName,
            $partitionId->getSqlValue(),
        );

        $this->connection->executeQuery($sql);
    }
}
