<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\Create;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Symfony\Component\Uid\Uuid;

final readonly class PostCreatedEvent
{
    private Post $post;

    public function __construct(
        private Uuid $id,
        private User $author,
        private string $title,
        private string $description,
    ) {
    }

    public function process(): Post
    {
        $this->post = new Post($this);

        $this->author->createPost($this);

        return $this->post;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
