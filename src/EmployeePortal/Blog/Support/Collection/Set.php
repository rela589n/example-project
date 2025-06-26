<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support\Collection;

use App\EmployeePortal\Blog\Support\EntityNotFoundException;
use App\EmployeePortal\Blog\Support\Specification\Specification;
use Doctrine\Common\Collections\Criteria;

final class Set implements Collection
{
    // db could have items before
    // db could have items in between
    // db could have items after

    private array $items = [];

    public function __construct(
        private(set) Specification $specification = new Specification([]),
        private ?self $parent = null,
    ) {
    }

    public function has(int|string $key): bool
    {
        return isset($this->items[$key]);
    }

    public function get(int|string $key): mixed
    {
        return $this->items[$key] ?? throw new EntityNotFoundException($key);
    }

    public function set(int|string $key, mixed $value): void
    {
        $this->parent?->set($key, $value);

        $this->items[$key] = $value;
    }

    public function matching(Criteria $criteria): self
    {
        return new self($this->specification->with($criteria), $this);
    }
}
