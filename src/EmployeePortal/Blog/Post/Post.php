<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use InvalidArgumentException;
use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Stories\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Stories\Edit\PostEditedEvent;
use App\EmployeePortal\Blog\Post\Stories\TransferOwnership\PostOwnershipTransferredEvent;
use App\EmployeePortal\Blog\User\User;
use Symfony\Component\Uid\Uuid;

class Post
{
    private Uuid $id;

    private User $author;

    private User $owner;

    private string $title;

    private string $description;

    private PostCommentCollection $comments;

    private PostCommentCollection $topComments;

    public function __construct(
        Uuid $id,
        PostCommentCollection $comments,
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

    public function assertIsOwnedBy(User $owner): void
    {
        if ($this->owner !== $owner) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }
    }
}
