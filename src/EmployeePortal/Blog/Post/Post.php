<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post;

use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Features\Edit\PostEditedEvent;
use App\EmployeePortal\Blog\Post\Features\TransferOwnership\PostOwnershipTransferredEvent;
use App\EmployeePortal\Blog\User\User;
use InvalidArgumentException;
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
        // if orderByRating is called multiple times, it should return the same object
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

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getComments(): PostCommentCollection
    {
        return $this->comments;
    }

    public function getTopComments(): PostCommentCollection
    {
        return $this->comments->orderByRating()->limit(10);
    }
}
