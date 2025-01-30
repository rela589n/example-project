<?php

declare(strict_types=1);

namespace App\Playground\Workflow\HelloWorld;

final readonly class HelloWorld
{
    public function __construct(
        private string $state,
    ) {
    }

    public function getState(): string
    {
        return $this->state;
    }
}
