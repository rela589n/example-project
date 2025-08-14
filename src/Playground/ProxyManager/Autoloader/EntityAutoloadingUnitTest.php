<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Autoloader;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntityAutoloader::class)]
final class EntityAutoloadingUnitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $entityAutoloader = EntityAutoloader::create();
        $entityAutoloader->register();
    }

    public function testEntityIsEntityProx(): void
    {
        $anEntity = new AnEntity('foo');

        self::assertInstanceOf(EntityProxy::class, $anEntity);
        self::assertInstanceOf(AnEntity::class, $anEntity);

        self::assertSame('foo_bar', $anEntity->foo);
    }
}
