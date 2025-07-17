<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

class SimpleProxiedObject
{
    public function __construct(
        protected(set) int $foo,
        protected(set) string $bar,
        protected string $baz,
    ) {
    }

    public function modify(int $foo, string $bar, string $baz): void
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function getThis(): static
    {
        return $this;
    }

    public function methodCall(): int
    {
        return 1;
    }
}
