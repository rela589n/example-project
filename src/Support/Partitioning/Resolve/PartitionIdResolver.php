<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Resolve;

use App\Support\Partitioning\Entity\PartitionId;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(PartitionIdResolver::class)]
interface PartitionIdResolver
{
    public function resolve(): ?PartitionId;
}
