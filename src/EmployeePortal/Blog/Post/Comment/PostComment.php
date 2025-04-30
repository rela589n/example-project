<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\PostCommentAddedEvent;
use App\EmployeePortal\Blog\Post\Comment\Features\Edit\PostCommentEditedEvent;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

class PostComment
{
    private Uuid $id;

    private User $author;

    private Post $post;

    private string $text;

    private CarbonImmutable $addedAt;

    public function add(PostCommentAddedEvent $event): void
    {
        $this->id = $event->getId();
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
            throw new InvalidArgumentException('Comment does not belong to this user');
        }
    }

    public function assertBelongsTo(Post $post): void
    {
        if ($this->post !== $post) {
            throw new InvalidArgumentException('Comment does not belong to this post');
        }
    }
}
