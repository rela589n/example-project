<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(ReflectionClass::class)]
final class NativeProxiesUnitTest extends TestCase
{
    /** @var ReflectionClass<ProxiedObject> */
    private ReflectionClass $reflectionClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reflectionClass = new ReflectionClass(ProxiedObject::class);
    }

    public function testExistingObjectCanBeResetAsProxy(): void
    {
        $object = new ProxiedObject(1, '2');

        $this->reflectionClass->resetAsLazyGhost($object, function (ProxiedObject $object) {
            self::assertFalse(isset($object->foo));
            self::assertFalse(isset($object->bar));

            $object->__construct(2, '3');
        });

        self::assertSame(3, $object->foo);
        self::assertSame('3', $object->bar);
    }

    public function testProxyInitializerMustNotReturnProxy(): void
    {
        $proxy = $this->reflectionClass->newLazyProxy(function () {
            $this->expectException(Error::class);
            $this->expectExceptionMessage('Lazy proxy factory must return a non-lazy object');

            return $this->reflectionClass->newLazyProxy(function () {
            });
        });

        /** @noinspection PhpExpressionResultUnusedInspection */
        $proxy->foo;
    }

    public function testTwoLazyProxiesCanFollowTheSameObject(): void
    {
        $object = new SimpleProxiedObject(1, '2', '3');

        $reflectionClass = new ReflectionClass(SimpleProxiedObject::class);

        $proxy1 = $reflectionClass->newLazyProxy(fn () => $object);
        self::assertSame(1, $proxy1->foo);

        $proxy2 = $reflectionClass->newLazyProxy(fn () => $object);
        self::assertSame(1, $proxy2->foo);

        $object->modify(2, '3', '4');

        self::assertSame(2, $proxy1->foo);
        self::assertSame(2, $proxy2->foo);
    }

    public function testProxyMethodsReturnCorrectThisOfItself(): void
    {
        $object = new SimpleProxiedObject(1, '2', '3');

        $reflectionClass = new ReflectionClass(SimpleProxiedObject::class);

        $proxy = $reflectionClass->newLazyProxy(fn () => $object);

        self::assertSame($proxy, $proxy->getThis());
        self::assertNotSame($object->getThis(), $proxy->getThis());
    }
}
