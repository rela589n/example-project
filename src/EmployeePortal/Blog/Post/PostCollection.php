<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use App\EmployeePortal\Blog\User\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityRepository;

final class PostCollection extends EntityRepository
{
    public function __construct(
        private ?self $parentCollection,
    ) {
    }

    public function ofOwner(User $user): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $this->matching(Criteria::create()->where($expr->eq('owner', $user)));
    }

    public function add(Post $post): void
    {
        $this->parentCollection?->add($post);
    }

    public function contains(Post $post): bool
    {

    }
}
