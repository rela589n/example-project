<?php

declare(strict_types=1);

namespace App\Playground\SPL\Iterators;

use AppendIterator;
use ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(AppendIterator::class)]
final class AppendIteratorUnitTest extends TestCase
{
    public function testAppendIterators(): void
    {
        $it1 = new ArrayIterator(['one', 'two']);
        $it2 = new ArrayIterator(['three', 'four']);

        $appendIterator = new AppendIterator();

        $appendIterator->append($it1);
        $appendIterator->append($it2);

        $resultNoKeys = iterator_to_array($appendIterator, false);
        self::assertSame(['one', 'two', 'three', 'four'], $resultNoKeys);

        $resultWithKeys = iterator_to_array($appendIterator);
        self::assertSame(['three', 'four'], $resultWithKeys);
    }
}
