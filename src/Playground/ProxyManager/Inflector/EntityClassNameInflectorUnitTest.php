<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Inflector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProxyManager\Inflector\ClassNameInflectorInterface;

/** @internal */
#[CoversClass(EntityClassNameInflector::class)]
final class EntityClassNameInflectorUnitTest extends TestCase
{
    /**
     * @param class-string $realClassName
     * @param class-string $proxyClassName
     */
    #[DataProvider('classNamesDataProvider')]
    public function testInflector(string $realClassName, string $proxyClassName): void
    {
        $inflector = new EntityClassNameInflector(('ProxyNS'));

        self::assertFalse($inflector->isProxyClassName($realClassName));
        self::assertTrue($inflector->isProxyClassName($proxyClassName));

        self::assertStringMatchesFormat($realClassName, $inflector->getUserClassName($realClassName));
        self::assertStringMatchesFormat($proxyClassName, $inflector->getProxyClassName($proxyClassName));
        self::assertStringMatchesFormat($proxyClassName, $inflector->getProxyClassName($realClassName));
        self::assertStringMatchesFormat($realClassName, $inflector->getUserClassName($proxyClassName));
    }

    public function testGeneratesSameClassNameWithSameParameters(): void
    {
        /** @var class-string $fooBar */
        $fooBar = 'Foo\\Bar';
        $inflector = new EntityClassNameInflector(('ProxyNS'));

        self::assertSame($inflector->getProxyClassName($fooBar), $inflector->getProxyClassName($fooBar));
        self::assertSame(
            $inflector->getProxyClassName($fooBar, ['baz' => 'tab']),
            $inflector->getProxyClassName($fooBar, ['baz' => 'tab']),
        );
        self::assertSame(
            $inflector->getProxyClassName($fooBar, ['tab' => 'baz']),
            $inflector->getProxyClassName($fooBar, ['tab' => 'baz']),
        );
    }

    public function testGeneratesDifferentClassNameWithDifferentParameters(): void
    {
        /** @var class-string $fooBar */
        $fooBar = 'Foo\\Bar';
        $inflector = new EntityClassNameInflector(('ProxyNS'));

        self::assertNotSame(
            $inflector->getProxyClassName($fooBar),
            $inflector->getProxyClassName($fooBar, ['foo' => 'bar']),
        );
        self::assertNotSame(
            $inflector->getProxyClassName($fooBar, ['baz' => 'tab']),
            $inflector->getProxyClassName($fooBar, ['tab' => 'baz']),
        );
        self::assertNotSame(
            $inflector->getProxyClassName($fooBar, ['foo' => 'bar',
                'tab' => 'baz']),
            $inflector->getProxyClassName($fooBar, ['foo' => 'bar']),
        );
        self::assertNotSame(
            $inflector->getProxyClassName($fooBar, ['foo' => 'bar',
                'tab' => 'baz']),
            $inflector->getProxyClassName($fooBar, ['tab' => 'baz',
                'foo' => 'bar']),
        );
    }

    public function testGeneratesCorrectClassNameWhenGivenLeadingBackslash(): void
    {
        /** @var class-string $fooBar */
        $fooBar = 'Foo\\Bar';
        /** @var class-string $fooBarPrefixed */
        $fooBarPrefixed = '\\Foo\\Bar';
        $inflector = new EntityClassNameInflector(('ProxyNS'));

        self::assertSame(
            $inflector->getProxyClassName($fooBarPrefixed, ['tab' => 'baz']),
            $inflector->getProxyClassName($fooBar, ['tab' => 'baz']),
        );
    }

    /**
     * @param class-string $className
     * @param array<string, mixed> $parameters
     */
    #[DataProvider('classParameterCombinationsDataProvider')]
    public function testClassNameIsValidClassIdentifier(string $className, array $parameters): void
    {
        $inflector = new EntityClassNameInflector(('ProxyNS'));

        self::assertMatchesRegularExpression(
            '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+)(\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+)*/',
            $inflector->getProxyClassName($className, $parameters),
            'Class name string is a valid class identifier',
        );
    }

    public static function classNamesDataProvider(): array
    {
        return [
            ['Foo', 'ProxyNS\\%s\\'.ClassNameInflectorInterface::PROXY_MARKER.'\\Foo'],
            ['Foo\\Bar', 'ProxyNS\\%s\\'.ClassNameInflectorInterface::PROXY_MARKER.'\\Foo\\Bar'],
        ];
    }

    public static function classParameterCombinationsDataProvider(): array
    {
        return [
            ['Foo', []],
            ['Foo\\Bar', []],
            ['Foo', [null]],
            ['Foo\\Bar', [null]],
            ['Foo', ['foo' => 'bar']],
            ['Foo\\Bar', ['foo' => 'bar']],
            ['Foo', ["\0" => "very \0 bad"]],
            ['Foo\\Bar', ["\0" => "very \0 bad"]],
        ];
    }
}
