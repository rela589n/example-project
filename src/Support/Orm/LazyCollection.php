<?php

declare(strict_types=1);

namespace App\Support\Orm;

use App\EmployeePortal\Blog\User\User;
use Closure;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Traversable;
use Webmozart\Assert\Assert;

final class LazyCollection implements Collection, Selectable
{
    private bool $isInitialized = false;

    private Collection&Selectable $collection;

    public function __construct(
        private EntityManagerInterface $entityManager,
        /** @var class-string */
        private string $className,
        (Collection&Selectable)|null $collection = null,
    ) {
        $this->collection = $collection ?? $entityManager->getRepository(User::class)->matching(Criteria::create());
    }

    public function add(mixed $element): void
    {
        Assert::object($element);

        $this->entityManager->persist($element);
        $this->collection->add($element);
    }

    public function clear(): void
    {
        $this->collection->clear();
    }

    public function remove(int|string $key): mixed
    {
        return $this->collection->remove($key);
    }

    public function removeElement(mixed $element): bool
    {
        return $this->collection->removeElement($element);
    }

    public function set(int|string $key, mixed $value): void
    {
        $this->collection->set($key, $value);
    }

    public function map(Closure $func): Collection
    {
        return $this->collection->map($func);
    }

    public function filter(Closure $p): Collection
    {
        return $this->collection->filter($p);
    }

    public function partition(Closure $p): array
    {
        return $this->collection->partition($p);
    }

    public function getIterator(): Traversable
    {
        return $this->collection->getIterator();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->collection->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->collection->offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->collection->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function contains(mixed $element): bool
    {
        return $this->collection->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    public function containsKey(int|string $key): bool
    {
        return $this->collection->containsKey($key);
    }

    public function getKeys(): array
    {
        return $this->collection->getKeys();
    }

    public function getValues(): array
    {
        return $this->collection->getValues();
    }

    public function toArray(): array
    {
        return $this->collection->toArray();
    }

    public function first(): mixed
    {
        return $this->collection->first();
    }

    public function last(): mixed
    {
        return $this->collection->last();
    }

    public function key(): int|string|null
    {
        return $this->collection->key();
    }

    public function current(): mixed
    {
        return $this->collection->current();
    }

    public function next(): mixed
    {
        return $this->collection->next();
    }

    public function slice(int $offset, ?int $length = null): array
    {
        return $this->collection->slice($offset, $length);
    }

    public function exists(Closure $p): bool
    {
        return $this->collection->exists($p);
    }

    public function forAll(Closure $p): bool
    {
        return $this->collection->forAll($p);
    }

    public function indexOf(mixed $element): int|string|false
    {
        return $this->collection->indexOf($element);
    }

    public function findFirst(Closure $p): mixed
    {
        return $this->collection->findFirst($p);
    }

    public function reduce(Closure $func, mixed $initial = null): mixed
    {
        return $this->collection->reduce($func, $initial);
    }

    public function matching(Criteria $criteria): ReadableCollection
    {
        /** @var Collection&Selectable $collection */
        $collection = $this->collection->matching($criteria);

        return new self($this->entityManager, $this->className, $collection);
    }

    public function get(int|string $key): mixed
    {
        if ($this->isInitialized) {
            return $this->collection->get($key);
        }

        $entity = $this->entityManager->find($this->className, $key);

        // fixme: check that criteria actually matches

        return $entity;
    }
}
