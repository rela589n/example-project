<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Actions\Edit;

use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;

final readonly class PostEditedEvent
{
    public function __construct(
        private User $editor,
        private Post $post,
        private string $title,
        private string $description,
    ) {
    }

    public function process(): void
    {
        $this->editor->editPost($this);
    }

    public function getEditor(): User
    {
        return $this->editor;
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
