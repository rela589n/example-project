<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User;

use EmployeePortal\Blog\Domain\User\Post\Actions\Create\PostCreatedEvent;
use EmployeePortal\Blog\Domain\User\Post\Actions\Edit\PostEditedEvent;
use EmployeePortal\Blog\Domain\User\Post\Actions\TransferOwnership\PostOwnershipTransferredEvent;
use EmployeePortal\Blog\Domain\User\Post\Comment\Actions\Create\PostCommentCreatedEvent;
use EmployeePortal\Blog\Domain\User\Post\Comment\Actions\Edit\PostCommentEditedEvent;
use EmployeePortal\Blog\Domain\User\Post\Comment\CommentCollection;
use EmployeePortal\Blog\Domain\User\Post\PostCollection;
use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;

    // anything you would like to control access to, should likely be declared as a collection
    private PostCollection $posts;

    private CommentCollection $comments;

    public function __construct(
        Uuid $id,
        PostCollection $postCollection,
        CommentCollection $commentCollection,
    ) {
        $this->id = $id;
        $this->posts = $postCollection->ofOwner($this);
        $this->comments = $commentCollection->ofAuthor($this);
    }

    public function createPost(PostCreatedEvent $event): void
    {
        $post = $event->getPost();

        $post->create($event);

        $this->posts->add($post);
    }

    public function editPost(PostEditedEvent $event): void
    {
        $post = $event->getPost();

        // this method should not load the collection, but rather use preloaded Post object that already belongs to this collection
        if (!$this->posts->contains($post)) {
            throw new \InvalidArgumentException('Post does not belong to this user');
        }

        $post->edit($event);
    }

    public function comment(PostCommentCreatedEvent $event): void
    {
        $comment = $event->getComment();

        $comment->add($event);

        $this->comments->add($comment);
    }

    public function editComment(PostCommentEditedEvent $event): void
    {
        $comment = $event->getComment();

        // this method should not load the collection, but rather use preloaded Comment object that already belongs to this collection
        if (!$this->comments->contains($comment)) {
            throw new \InvalidArgumentException('Comment does not belong to this user');
        }

        $comment->edit($event);
    }

    public function transferPostOwnership(PostOwnershipTransferredEvent $event): void
    {
        $post = $event->getPost();

        if (!$this->posts->contains($post)) {
            throw new \InvalidArgumentException('Post does not belong to this user');
        }

        $post->transferOwnership($event);
    }
}
