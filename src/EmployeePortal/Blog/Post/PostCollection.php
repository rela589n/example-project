<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Selectable;
use Symfony\Component\Uid\Uuid;

final readonly class PostCollection
{
    public function __construct(
        /** @var Selectable<Post>&Collection<Post>|\App\EmployeePortal\Blog\Support\Collection\Collection */
        private object $collection,
    ) {
    }

    public function ofOwner(Uuid $userId): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $criteria = Criteria::create()->where($expr->eq('owner', $userId));

        return new self($this->collection->matching($criteria));
    }

    public function contains(Post $post): bool
    {
        // get method should just load only this one post
        return $this->collection->has($post->getId()->toRfc4122());
    }

    public function add(Post $post): void
    {
        $this->collection->set($post->getId()->toRfc4122(), $post);
    }
}
