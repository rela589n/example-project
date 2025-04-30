<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Add;

use App\EmployeePortal\Blog\Post\Comment\PostComment;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class PostCommentAddedEvent
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
