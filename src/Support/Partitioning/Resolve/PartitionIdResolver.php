<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Resolve;

use App\Support\Partitioning\Entity\PartitionId;

interface PartitionIdResolver
{
    public function resolve(): ?PartitionId;
}
