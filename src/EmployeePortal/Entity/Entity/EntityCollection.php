<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity;

use App\Support\Orm\EntityNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Uid\Uuid;

#[Autoconfigure(constructor: 'create')]
final readonly class EntityCollection
{
    public function __construct(
        /** @var Selectable<array-key,Entity> */
        private Selectable $repository = new ArrayCollection(),
        private Criteria $criteria = new Criteria(),
    ) {
    }

    public static function create(EntityManagerInterface $entityManager): self
    {
        return new self($entityManager->getRepository(Entity::class));
    }

    public function whereId(Uuid $id): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        return $this->andWhere($expr->eq('id', $id->toRfc4122()));
    }

    /** @return ReadableCollection<array-key,Entity> */
    public function match(): ReadableCollection
    {
        return $this->repository->matching($this->criteria);
    }

    public function get(Uuid $id): Entity
    {
        return $this->whereId($id)->match()->first() ?: throw new EntityNotFoundException($id);
    }

    private function andWhere(Expression $expression): self
    {
        $criteria = clone $this->criteria;

        $criteria->andWhere($expression);

        return new self($this->repository, $criteria);
    }
}
