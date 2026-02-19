<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\_Features\Add;

use App\EmployeePortal\Blog\Post\Comment\PostComment;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class PostCommentAddedEvent
{
    private PostComment $comment;

    public function __construct(
        private Uuid $id,
        private User $author,
        private Post $post,
        private string $text,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function process(): PostComment
    {
        $this->comment = new PostComment($this);

        $this->author->comment($this);

        return $this->comment;
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
