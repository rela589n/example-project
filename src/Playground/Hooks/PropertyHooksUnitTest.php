<?php

declare(strict_types=1);

namespace App\Playground\Hooks;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpyOnHookedObject::class)]
final class PropertyHooksUnitTest extends TestCase
{
    public function testSpyOnProperty(): void
    {
        $hookedObject = new SpyOnHookedObject(1);

        self::assertSame(3, $hookedObject->foo);
        self::assertSame([], $hookedObject->getChanges());

        $hookedObject->foo = 4;
        $hookedObject->foo = 5;

        self::assertSame(7, $hookedObject->foo);
        self::assertSame(['foo' => [6, 7]], $hookedObject->getChanges());
    }

    public function testParentPropertyHookCallCanBeUsedWhenThereIsNoHook(): void
    {
        $hookedObject = new SpyOnHookedObject(1);

        $hookedObject->bar = 3;

        self::assertSame([
            'bar' => [3],
        ], $hookedObject->getChanges());
    }
}
