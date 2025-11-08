<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TypeError;

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

        self::assertSame(3, $object->foo); // hook
        self::assertSame('3', $object->bar);
    }

    public function testGhostCanBeResetAsProxy(): void
    {
        $object = $this->reflectionClass->newLazyGhost(function (ProxiedObject $object) {
            $object->__construct(1, '2');
        });

        self::assertSame(2, $object->foo);

        $this->reflectionClass->resetAsLazyProxy($object, function (ProxiedObject $object) {
            self::assertFalse(isset($object->foo));
            self::assertFalse(isset($object->bar));

            return new ProxiedObject(3, '4');
        });

        self::assertSame(4, $object->foo);
        self::assertSame('4', $object->bar);
    }

    public function testProxyInitializerMustNotReturnProxy(): void
    {
        $proxy = $this->reflectionClass->newLazyProxy(function () {
            return $this->reflectionClass->newLazyProxy(function () {
            });
        });

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Lazy proxy factory must return a non-lazy object');

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

    public function testExistingObjectCanNotBeResetToLazyProxyForChildObject(): void
    {
        $object = new ProxiedObject(1, '2');

        $reflectionClass = new ReflectionClass(ProxiedObject::class);

        $reflectionClass->resetAsLazyProxy($object, fn () => new ChildProxiedObject(1, '2'));

        $this->expectExceptionMessage('The real instance class App\Playground\ProxyManager\Tests\ChildProxiedObject is not compatible with the proxy class App\Playground\ProxyManager\Tests\ProxiedObject. The proxy must be a instance of the same class as the real instance, or a sub-class with no additional properties, and no overrides of the __destructor or __clone methods.');
        $this->expectException(TypeError::class);

        /** @noinspection PhpExpressionResultUnusedInspection */
        $object->foo;
    }
}
