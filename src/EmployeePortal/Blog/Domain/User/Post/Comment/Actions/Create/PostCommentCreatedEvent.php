<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User\Post\Comment\Actions\Create;

use Carbon\CarbonImmutable;
use EmployeePortal\Blog\Domain\User\Post\Comment\Comment;
use EmployeePortal\Blog\Domain\User\Post\Post;
use EmployeePortal\Blog\Domain\User\User;

final readonly class PostCommentCreatedEvent
{
    public function __construct(
        private User $author,
        private Post $post,
        private Comment $comment,
        private string $text,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function invoke(): void
    {
        $this->author->comment($this);
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }
}
