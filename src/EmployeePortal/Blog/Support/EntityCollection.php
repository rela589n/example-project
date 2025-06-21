<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Support;

use App\EmployeePortal\Blog\Support\Specification\Specification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

/**
 * @implements Collection
 * @implements Selectable
 */
final class EntityCollection
{
    public function __construct(
        private EntityPersister $persister,
        private Set $itemsSet = new Set(),
    ) {
    }

    public function get(int|string $key)
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
        );
    }
}
