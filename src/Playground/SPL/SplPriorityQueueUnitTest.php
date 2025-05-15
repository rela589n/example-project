<?php

declare(strict_types=1);

namespace App\Playground\SPL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SplPriorityQueue;

#[CoversClass(SplPriorityQueue::class)]
final class SplPriorityQueueUnitTest extends TestCase
{
    public function testInsertIntoHeapAndExtractFromIt(): void
    {
        $queue = new SplPriorityQueue();

        $queue->insert('three', 3);
        $queue->insert('one1', 1);
        $queue->insert('two', 2);
        $queue->insert('one2', 1);

        self::assertSame('three', $queue->extract());
        self::assertSame('two', $queue->extract());
        self::assertSame('one2', $queue->extract()); // not very correct behavior
        self::assertSame('one1', $queue->extract());
    }
}
