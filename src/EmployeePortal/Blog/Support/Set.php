<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support;

use App\EmployeePortal\Blog\Support\Specification\Specification;
use Doctrine\Common\Collections\Criteria;

final class Set
{
    // db could have items before
    // db could have items in between
    // db could have items after

    private array $items = [];

    public function __construct(
        private(set) Specification $specification = new Specification([]),
    ) {
    }

    public function matching(Criteria $criteria): self
    {
        return new self($this->specification->with($criteria));
    }

    public function get(int|string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function set(int|string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }
}
