<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use Symfony\Component\Uid\Uuid;

// any of the methods, if called multiple times, should return the same instance as previously
final readonly class PostCommentCollection
{
    public function __construct(
        /** @var Selectable<PostComment>&Collection<PostComment> */
        private Collection $collection = new ArrayCollection(),
    ) {
    }

    public function ofPost(Post $post): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $criteria = Criteria::create()->where($expr->eq('post', $post));

        return new self($this->collection->matching($criteria));
    }

    public function ofAuthor(User $user): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $criteria = Criteria::create()->where($expr->eq('author', $user));

        return new self($this->collection->matching($criteria));
    }

    public function add(PostComment $comment): void
    {
        $this->collection->add($comment);
    }

    public function orderByRating(): self
    {
        $orderCriteria = Criteria::create()->orderBy(['rating' => Order::Descending]);

        return new self($this->collection->matching($orderCriteria));
    }

    public function contains(PostComment $comment): bool
    {
        // get method should just load only this one comment
        return $this->collection->get($comment->getId()->toString()) !== null;
    }

    public function limit(int $limit): self
    {
        $maxResultsCriteria = Criteria::create()->setMaxResults($limit);

        return new self($this->collection->matching($maxResultsCriteria));
    }

    public function get(Uuid $id): PostComment
    {
        $comment = $this->collection->get($id->toString());

        if ($comment === null) {
            throw new \InvalidArgumentException('Comment not found');
        }

        return $comment;
    }

    public static function empty(): PostCommentCollection
    {
        return new self();
    }
}
