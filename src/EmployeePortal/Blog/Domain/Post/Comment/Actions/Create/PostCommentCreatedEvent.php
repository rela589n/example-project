<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\Post\Comment\Actions\Create;

use Carbon\CarbonImmutable;
use EmployeePortal\Blog\Domain\Post\Comment\PostComment;
use EmployeePortal\Blog\Domain\Post\Post;
use EmployeePortal\Blog\Domain\User\User;
use Symfony\Component\Uid\Uuid;

final readonly class PostCommentCreatedEvent
{
    public function __construct(
        private Uuid $id,
        private User $author,
        private Post $post,
        private PostComment $comment,
        private string $text,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(): void
    {
        $this->author->comment($this);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getComment(): PostComment
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
