<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\GhostObjectInterface;
use ProxyManager\Proxy\VirtualProxyInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TypeError;

#[CoversClass(LazyLoadingGhostFactory::class)]
#[CoversClass(LazyLoadingValueHolderFactory::class)]
final class LazyLoadingProxyIntegrationTest extends KernelTestCase
{
    private LazyLoadingGhostFactory $ghostFactory;

    private LazyLoadingValueHolderFactory $valueHolderFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        /** @var LazyLoadingGhostFactory $ghostFactory */
        $ghostFactory = $container->get('app_proxy_manager.factory.lazy_loading_ghost');
        $this->ghostFactory = $ghostFactory;

        /** @var LazyLoadingValueHolderFactory $valueHolderFactory */
        $valueHolderFactory = $container->get('app_proxy_manager.factory.lazy_loading_value_holder');
        $this->valueHolderFactory = $valueHolderFactory;

    }

    public function testGhostedHookedPropertiesAreProblematic(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot unset hooked property ProxyManagerGeneratedProxy\Generatedfcde5888318403a5033f7bf87317fd2b\__PM__\App\Playground\ProxyManager\Tests\ProxiedObject::$foo');

        $this->ghostFactory->createProxy(
            ProxiedObject::class,
            function () { // @phpstan-ignore argument.type
            },
        );
    }

    public function testValueHolderHookedPropertiesAreProblematic(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot unset hooked property ProxyManagerGeneratedProxy\Generated2969ca5f827c3346e66e3378707db48e\__PM__\App\Playground\ProxyManager\Tests\ProxiedObject::$foo');

        $this->valueHolderFactory->createProxy(
            ProxiedObject::class,
            function () { // @phpstan-ignore argument.type
            },
        );
    }

    public function testNativeObjectProxyCanNotFollowObjectOfChildClass(): void
    {
        $reflectionClass = new ReflectionClass(SimpleProxiedObject::class);

        $proxy = $reflectionClass->newLazyProxy(function () {
            return $this->createGhostProxy();
        });

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The real instance class ProxyManagerGeneratedProxy\Generated90b862e9716a786e470e2052e11950ef\__PM__\App\Playground\ProxyManager\Tests\SimpleProxiedObject is not compatible with the proxy class App\Playground\ProxyManager\Tests\SimpleProxiedObject. The proxy must be a instance of the same class as the real instance, or a sub-class with no additional properties, and no overrides of the __destructor or __clone methods.');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $proxy->foo; // @phpstan-ignore expr.resultUnused
    }

    public function testProxyIsGraduallyInitialized(): void
    {
        $proxy = $this->createValueHolderProxy();

        self::assertSame(123, $proxy->foo);
        self::assertSame('test', $proxy->bar);
        self::assertSame(1, $proxy->methodCall());

        self::assertSame($proxy, $proxy->getThis());

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot access protected property');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $proxy->baz; // @phpstan-ignore property.protected, expr.resultUnused
    }

    public function testNativeProxyOverGhostFollowingGhost(): void
    {
        $ghostProxy = $this->createGhostProxy();

        $reflectionClass = new ReflectionClass($ghostProxy::class);

        /** @var SimpleProxiedObject $proxy */
        $proxy = $reflectionClass->newLazyProxy(fn () => $ghostProxy);

        self::assertSame(123, $proxy->foo);
    }

    public function testResetObjectAsGhostProxyIsNotPossible(): void
    {
        $object = new SimpleProxiedObject(1, '2', '3');

        $ghostProxy = $this->createGhostProxy();

        $reflectionClass = new ReflectionClass(SimpleProxiedObject::class);

        $reflectionClass->resetAsLazyProxy($object, function () use ($ghostProxy) {
            return $ghostProxy;
        });

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('The real instance class ProxyManagerGeneratedProxy\Generated90b862e9716a786e470e2052e11950ef\__PM__\App\Playground\ProxyManager\Tests\SimpleProxiedObject is not compatible with the proxy class App\Playground\ProxyManager\Tests\SimpleProxiedObject. The proxy must be a instance of the same class as the real instance, or a sub-class with no additional properties, and no overrides of the __destructor or __clone methods.');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $object->foo; // @phpstan-ignore expr.resultUnused
    }

    private function createValueHolderProxy(): SimpleProxiedObject
    {
        return $this->valueHolderFactory->createProxy(
            SimpleProxiedObject::class,
            function (&$wrappedObject, SimpleProxiedObject&VirtualProxyInterface $proxy, string $method, array $parameters) { // @phpstan-ignore argument.type, typeCoverage.paramTypeCoverage
                $wrappedObject = $proxy->getWrappedValueHolderValue();

                if (null === $wrappedObject) {
                    $wrappedObject = $this->createGhostProxy();
                }
            },
            ['fluentSafe' => true],
        );
    }

    private function createGhostProxy(): SimpleProxiedObject&GhostObjectInterface
    {
        $initializer = function (SimpleProxiedObject&GhostObjectInterface $proxy, string $method, array $parameters) {
            if ('__set' === $method) {
                // set value on all the proxies
            }

            if ('foo' === $parameters['name']) {

                $setFoo = fn () => $proxy->foo = 123; // @phpstan-ignore assign.propertyProtectedSet
                $setFoo->bindTo($proxy, SimpleProxiedObject::class)();
            }

            if ('bar' === $parameters['name']) {
                $setBar = fn () => $proxy->bar = 'test'; // @phpstan-ignore assign.propertyProtectedSet
                $setBar->bindTo($proxy, SimpleProxiedObject::class)();
            }

            if ('baz' === $parameters['name']) {
                $setBaz = fn () => $proxy->baz = 'baz'; // @phpstan-ignore property.protected
                $setBaz->bindTo($proxy, SimpleProxiedObject::class)();
            }
        };

        $proxy = $this->ghostFactory->createProxy(
            SimpleProxiedObject::class,
            $initializer, // @phpstan-ignore argument.type
        );

        // $proxy->setProxyInitializer($initializer->bindTo($proxy, $proxy::class));

        return $proxy;
    }
}
