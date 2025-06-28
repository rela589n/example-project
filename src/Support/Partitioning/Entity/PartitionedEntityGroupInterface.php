<?php

declare(strict_types=1);

namespace App\Support\Partitioning\Entity;

interface PartitionedEntityGroupInterface
{
    public static function getPartitionGroup(): string;
}
