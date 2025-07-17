<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\User\Support;

use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\PostCollection;
use App\EmployeePortal\Blog\User\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, entity: User::class)]
final readonly class UserSetupListener
{
    public function __construct(
        private(set) PostCollection $postCollection,
        private(set) PostCommentCollection $postCommentCollection,
    ) {
    }

    /** @api */
    public function postLoad(User $user): void
    {
        (fn (User $user) => $user->posts = $this->postCollection)->bindTo($this, User::class)($user);
        (fn (User $user) => $user->comments = $this->postCommentCollection)->bindTo($this, User::class)($user);
    }
}
