<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment;

use App\Support\Orm\EntityNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Uid\Uuid;

#[Autoconfigure(constructor: 'create')]
final readonly class PostCommentCollection
{
    public function __construct(
        /** @var Selectable<array-key,PostComment> */
        private Selectable $repository = new ArrayCollection(),
        private Criteria $criteria = new Criteria(),
    ) {
    }

    public static function create(EntityManagerInterface $entityManager): self
    {
        return new self($entityManager->getRepository(PostComment::class));
    }

    public function ofPost(Uuid $postId): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        /** @see PostComment::$post */
        return $this->andWhere($expr->eq('post', $postId));
    }

    public function ofAuthor(Uuid $userId): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        /** @see PostComment::$author */
        return $this->andWhere($expr->eq('author', $userId));
    }

    public function whereId(Uuid $id): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        return $this->andWhere($expr->eq('id', $id->toRfc4122()));
    }

    public function add(PostComment $comment): void
    {
        // $this->repository->set($comment->getId()->toRfc4122(), $comment);
    }

    public function orderByRating(): self
    {
        $criteria = clone $this->criteria;
        $criteria->orderBy(['rating' => Order::Descending]);

        return new self($this->repository, $criteria);
    }

    public function contains(PostComment $comment): bool
    {
        // get method should just load only this one comment
        return !empty($this->whereId($comment->id)->match()->first());
    }

    public function limit(int $limit): self
    {
        $criteria = clone $this->criteria;
        $criteria->setMaxResults($limit);

        return new self($this->repository, $criteria);
    }

    /** @return ReadableCollection<array-key,PostComment> */
    public function match(): ReadableCollection
    {
        return $this->repository->matching($this->criteria);
    }

    public function get(Uuid $id): PostComment
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
