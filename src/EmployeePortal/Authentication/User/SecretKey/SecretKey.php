<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey;

final readonly class SecretKey
{
    public function __construct(
        private string $key,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function equals(self $other): bool
    {
        return $this->key === $other->key;
    }
}
