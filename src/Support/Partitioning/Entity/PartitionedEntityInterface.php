<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Entity;

interface PartitionedEntityInterface
{
    public function getPartitionId(): PartitionId;
}
