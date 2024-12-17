<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User\Post;

use EmployeePortal\Blog\Domain\User\Post\Actions\Create\PostCreatedEvent;
use EmployeePortal\Blog\Domain\User\Post\Actions\Edit\PostEditedEvent;
use EmployeePortal\Blog\Domain\User\Post\Actions\TransferOwnership\PostOwnershipTransferredEvent;
use EmployeePortal\Blog\Domain\User\Post\Comment\CommentCollection;
use EmployeePortal\Blog\Domain\User\User;
use Symfony\Component\Uid\Uuid;

class Post
{
    private Uuid $id;

    private User $author;

    private User $owner;

    private string $title;

    private string $description;

    private CommentCollection $comments;

    private CommentCollection $topComments;

    public function __construct(
        Uuid $id,
        CommentCollection $comments,
    ) {
        $this->id = $id;
        $this->comments = $comments->ofPost($this);
        // loading $this->topComments shouldn't load $this->comments;
        // loading $this->comments should load $this->topComments
        // if during loading $this->comments, there were some comments matching the criteria, these should not be included for the database query (inner collection)
        $this->topComments = $this->comments->orderByRating()->limit(10);
    }

    public function create(PostCreatedEvent $event): void
    {
        $this->author = $event->getAuthor();
        $this->owner = $event->getAuthor();
        $this->title = $event->getTitle();
        $this->description = $event->getDescription();
    }

    public function edit(PostEditedEvent $event): void
    {
        $this->title = $event->getTitle();
        $this->description = $event->getDescription();
    }

    public function transferOwnership(PostOwnershipTransferredEvent $event): void
    {
        // this should trigger reactive collection change in User.posts (removed item),
        // also, new owner's collection should be updated to include this post
        $this->owner = $event->getNewOwner();
    }

    public function assertBelongsTo(User $owner): void
    {
        if ($this->owner !== $owner) {
            throw new \InvalidArgumentException('Post does not belong to this user');
        }
    }
}
