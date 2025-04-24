<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Stories\Create;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;

final readonly class PostCreatedEvent
{
    public function __construct(
        private User $author,
        private Post $post,
        private string $title,
        private string $description,
    ) {
    }

    public function process(): void
    {
        $this->author->createPost($this);
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
