<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support\Collection;

use Doctrine\Common\Collections\Criteria;

interface Collection
{
    public function has(int|string $key): bool;

    public function get(int|string $key): mixed;

    public function set(int|string $key, mixed $value): void;

    public function matching(Criteria $criteria): self;
}
