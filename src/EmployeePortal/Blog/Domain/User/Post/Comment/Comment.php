<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User\Post\Comment;

use Carbon\CarbonImmutable;
use EmployeePortal\Blog\Domain\User\Post\Comment\Actions\Create\PostCommentCreatedEvent;
use EmployeePortal\Blog\Domain\User\Post\Comment\Actions\Edit\PostCommentEditedEvent;
use EmployeePortal\Blog\Domain\User\Post\Post;
use EmployeePortal\Blog\Domain\User\User;
use Symfony\Component\Uid\Uuid;

class Comment
{
    private Uuid $id;

    private User $author;

    private Post $post;

    private string $text;

    private CarbonImmutable $addedAt;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function add(PostCommentCreatedEvent $event): void
    {
        $this->author = $event->getAuthor();
        $this->post = $event->getPost();
        $this->text = $event->getText();
        $this->addedAt = $event->getTimestamp();
    }

    public function edit(PostCommentEditedEvent $event)
    {

        $this->text = $event->getText();

    }

    public function getAddedAt(): CarbonImmutable
    {
        return $this->addedAt;
    }

    public function assertIsAuthoredBy(User $user): void
    {
        if ($this->author !== $user) {
            throw new \InvalidArgumentException('Comment does not belong to this user');
        }
    }

    public function assertBelongsTo(Post $post): void
    {
        if ($this->post !== $post) {
            throw new \InvalidArgumentException('Comment does not belong to this post');
        }
    }
}
