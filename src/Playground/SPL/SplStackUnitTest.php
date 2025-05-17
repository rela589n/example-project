<?php

declare(strict_types=1);

namespace App\Playground\SPL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SplStack;

#[CoversClass(SplStack::class)]
final class SplStackUnitTest extends TestCase
{
    public function testInsertIntoStackAndExtractFromIt(): void
    {
        $stack = new SplStack();

        $stack->push('one');
        $stack->push('two');
        $stack->push('three');

        self::assertSame('three', $stack->top());
        self::assertSame('three', $stack->pop());

        self::assertSame('two', $stack->top());
        self::assertSame('two', $stack->pop());

        self::assertSame('one', $stack->top());
        self::assertSame('one', $stack->pop());

        self::assertTrue($stack->isEmpty());
    }
}
