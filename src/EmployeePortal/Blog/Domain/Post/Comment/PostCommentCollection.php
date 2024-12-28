<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\Post\Comment;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityRepository;
use EmployeePortal\Blog\Domain\Post\Post;
use EmployeePortal\Blog\Domain\User\User;

final class PostCommentCollection extends EntityRepository
{
    public function ofPost(Post $post): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $this->matching(Criteria::create()->where($expr->eq('post', $post)));
    }

    public function ofAuthor(User $user): self
    {
        /** @var ExpressionBuilder $expr */
        $expr = Criteria::expr();

        $this->matching(Criteria::create()->where($expr->eq('author', $user)));
    }

    public function add(PostComment $comment): void
    {
        $this->parentCollection?->add($comment);
    }

    public function orderByRating(): self
    {

    }

    public function contains(PostComment $comment): bool
    {

    }

    public function limit(int $int): self
    {

    }
}
