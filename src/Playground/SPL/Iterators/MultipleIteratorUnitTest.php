<?php

declare(strict_types=1);

namespace App\Playground\SPL\Iterators;

use ArrayIterator;
use MultipleIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipleIterator::class)]
final class MultipleIteratorUnitTest extends TestCase
{
    public function testMultipleIterator(): void
    {
        $it1 = new ArrayIterator(['one', 'two']);
        $it2 = new ArrayIterator(['three', 'four']);

        $multipleIterator = new MultipleIterator();
        $multipleIterator->attachIterator($it1);
        $multipleIterator->attachIterator($it2);

        $result = iterator_to_array($multipleIterator, false);
        self::assertSame([['one', 'three'], ['two', 'four']], $result);
    }
}
