<?php

declare(strict_types=1);

namespace App\Playground\SPL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use WeakMap;

use function gc_collect_cycles;

#[CoversClass(WeakMap::class)]
final class WeakMapUnitTest extends TestCase
{
    public function testWeakMap(): void
    {
        $weakMap = new WeakMap();
        $entityManager = new stdClass();

        $weakMap[$entityManager] = $this->getPartitionManager($entityManager);

        self::assertCount(1, $weakMap);

        unset($entityManager);
        gc_collect_cycles(); // collect cyclic reference

        self::assertCount(0, $weakMap);
    }

    private function getPartitionManager(stdClass $entityManager): stdClass
    {
        $partitionManager = new stdClass();
        $partitionManager->entityManager = $entityManager;

        return $partitionManager;
    }
}
