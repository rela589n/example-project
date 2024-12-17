<?php

declare(strict_types=1);

namespace EmployeePortal\Blog\Domain\User\Post\Actions\Edit;

use EmployeePortal\Blog\Domain\User\Post\Post;
use EmployeePortal\Blog\Domain\User\User;

final readonly class PostEditedEvent
{
    public function __construct(
        private User $editor,
        private Post $post,
        private string $title,
        private string $description,
    ) {
    }

    public function invoke(): void
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
