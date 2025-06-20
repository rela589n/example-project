<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use App\EmployeePortal\Blog\User\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Selectable;

final readonly class PostCollection
{
    public function __construct(
        /** @var Selectable<Post>&Collection<Post> */
        private Collection $collection,
    ) {
    }

    public function ofOwner(User $user): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $criteria = Criteria::create()->where($expr->eq('owner', $user));

        return new self($this->collection->matching($criteria));
    }

    public function add(Post $post): void
    {
        $this->collection->set($post->getId()->toRfc4122(), $post);
    }

    public function contains(Post $post): bool
    {
        // get method should just load only this one post
        return $this->collection->get($post->getId()->toRfc4122()) !== null;
    }
}
