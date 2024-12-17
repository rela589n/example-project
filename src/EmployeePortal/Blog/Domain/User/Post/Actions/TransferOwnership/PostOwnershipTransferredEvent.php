<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User\Post\Actions\TransferOwnership;

use EmployeePortal\Blog\Domain\User\Post\Post;
use EmployeePortal\Blog\Domain\User\User;

final readonly class PostOwnershipTransferredEvent
{
    public function __construct(
        private User $owner,
        private User $newOwner,
        private Post $post,
    ) {
    }

    public function invoke(): void
    {
        $this->post->assertBelongsTo($this->owner);

        $this->owner->transferPostOwnership($this);
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getNewOwner(): User
    {
        return $this->newOwner;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}
