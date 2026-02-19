<?php

declare(strict_types=1);

namespace App\Support\Orm\Collection;

use App\EmployeePortal\Blog\_Support\Specification\Specification;
use App\Support\Orm\EntityNotFoundException;
use Doctrine\Common\Collections\Criteria;

final class Set implements Collection
{
    // db could have items before
    // db could have items in between
    // db could have items after

    /** @var array<int|string, mixed> */
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
