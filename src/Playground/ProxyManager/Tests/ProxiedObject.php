<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

class ProxiedObject
{
    public function __construct(
        protected(set) int $foo {
            set => $value + 1;
        },
        protected(set) string $bar,
    ) {
    }
}
