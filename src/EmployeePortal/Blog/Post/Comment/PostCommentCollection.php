<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityRepository;

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
