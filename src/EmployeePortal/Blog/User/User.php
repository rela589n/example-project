<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\User;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\PostCommentAddedEvent;
use App\EmployeePortal\Blog\Post\Comment\Features\Edit\PostCommentEditedEvent;
use App\EmployeePortal\Blog\Post\Comment\PostCommentCollection;
use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use App\EmployeePortal\Blog\Post\Features\Edit\PostEditedEvent;
use App\EmployeePortal\Blog\Post\Features\TransferOwnership\PostOwnershipTransferredEvent;
use App\EmployeePortal\Blog\Post\PostCollection;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;

    // anything you would like to control access to should likely be declared as a collection
    private PostCollection $posts;

    private PostCommentCollection $comments;

    public function __construct(
        Uuid $id,
        PostCollection $postCollection,
        PostCommentCollection $commentCollection,
    ) {
        $this->id = $id;
        $this->posts = $postCollection->ofOwner($this);
        $this->comments = $commentCollection->ofAuthor($this);
    }

    public function createPost(PostCreatedEvent $event): void
    {
        $event->getPost()->create($event);

        $this->posts->add($event->getPost());
    }

    public function editPost(PostEditedEvent $event): void
    {
        $post = $event->getPost();

        // this method should not load the collection, but rather use preloaded Post object that already belongs to this collection
        if (!$this->posts->contains($post)) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }

        $post->edit($event);
    }

    public function comment(PostCommentAddedEvent $event): void
    {
        $comment = $event->getComment();

        $comment->add($event);

        $this->comments->add($comment);
    }

    public function editComment(PostCommentEditedEvent $event): void
    {
        $comment = $event->getComment();

        // this method should not load the collection but rather use preloaded Comment object that already belongs to this collection
        if (!$this->comments->contains($comment)) {
            throw new InvalidArgumentException('Comment does not belong to this user');
        }

        $comment->edit($event);
    }

    public function transferPostOwnership(PostOwnershipTransferredEvent $event): void
    {
        $post = $event->getPost();

        if (!$this->posts->contains($post)) {
            throw new InvalidArgumentException('Post does not belong to this user');
        }

        $post->transferOwnership($event);
    }
}
