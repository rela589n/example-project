<?php

declare(strict_types=1);

namespace App\Playground\Hooks;

class HookedObject
{
    public function __construct(
        protected(set) int $foo {
            set => $value + 2;
        },
        protected int $bar = 2,
    ) {
    }
}
