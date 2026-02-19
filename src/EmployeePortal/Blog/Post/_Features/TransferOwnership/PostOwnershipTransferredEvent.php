<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\TransferOwnership;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;

final readonly class PostOwnershipTransferredEvent
{
    public function __construct(
        private User $owner,
        private User $newOwner,
        private Post $post,
    ) {
    }

    public function process(): void
    {
        $this->post->assertIsOwnedBy($this->owner);

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
