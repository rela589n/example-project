<?php

declare(strict_types=1);

namespace App\Support\Orm\Collection;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

final class EntityCollection implements Collection
{
    public function __construct(
        private EntityPersister $persister,
        private Set $itemsSet = new Set(),
    ) {
    }

    public function has(int|string $key): bool
    {

    }

    public function get(int|string $key): mixed
    {
        $entity = $this->itemsSet->get($key);

        if (null !== $entity) {
            return $entity;
        }

        $criteria = $this->itemsSet->specification->getCriteria();

        // this method should load the entity, save it and return it
    }

    public function set(int|string $key, mixed $value): void
    {
        $this->itemsSet->set($key, $value);
    }

    public function matching(Criteria $criteria): self
    {
        return new self(
            $this->persister,
            // it could be the case that Criteria:
            // 1. contains conditions
            // 2. contains ordering
            // 3. contains some limit
            //
            // Even though the set could be reached just with the loaded items,
            // there's no guarantee that there are no other, that if loaded,
            // would be prior to the existing items, and thus represent the requested set more correctly
            $this->itemsSet->matching($criteria),
            // if the collection is loaded completely, every request must result in memory-lookup
        );
    }
}
