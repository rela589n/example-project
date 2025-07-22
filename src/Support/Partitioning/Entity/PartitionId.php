<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Entity;

use function is_string;
use function sprintf;

final class PartitionId
{
    public function __construct(
        private readonly int|string $id,
        private ?string $tableNameSuffix = null,
    ) {
        $this->tableNameSuffix ??= (string)$this->id;
    }

    public function getSqlValue(): string
    {
        return is_string($this->id) ? sprintf("'%s'", $this->id) : (string)$this->id;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getTableName(string $mainTableName): string
    {
        return sprintf('%s_%s', $mainTableName, $this->tableNameSuffix);
    }
}
