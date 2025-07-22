<?php

declare(strict_types=1);

namespace App\Playground\SPL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SplMinHeap;

#[CoversClass(SplMinHeap::class)]
final class SplMinHeapUnitTest extends TestCase
{
    public function testInsertIntoHeapAndExtractFromIt(): void
    {
        $heap = self::newHeap();

        $heap->insert([1, 'one1']);
        $heap->insert([3, 'three']);
        $heap->insert([2, 'two']);
        $heap->insert([1, 'one2']);

        self::assertSame([1, 'one1'], $heap->extract());
        self::assertSame([1, 'one2'], $heap->extract());
        self::assertSame([2, 'two'], $heap->extract());
        self::assertSame([3, 'three'], $heap->extract());
    }

    private static function newHeap(): SplMinHeap
    {
        return new class() extends SplMinHeap {
            protected function compare(mixed $value1, mixed $value2): int
            {
                /** @var list<int|string> $value1 */
                /** @var list<int|string> $value2 */
                [$v1] = $value1;
                [$v2] = $value2;

                return parent::compare($v1, $v2);
            }
        };
    }
}
