<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Edit;

use App\EmployeePortal\Blog\Post\Comment\PostComment;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use RuntimeException;

final readonly class PostCommentEditedEvent
{
    private const MAX_EDITION_HOURS = 2;

    public function __construct(
        private User $editor,
        private Post $post,
        private PostComment $comment,
        private string $text,
        private CarbonImmutable $timestamp,
    ) {
    }

    public function invoke(): void
    {
        $this->comment->assertIsAuthoredBy($this->editor);
        $this->comment->assertBelongsTo($this->post);

        if ($this->timestamp->diff($this->comment->getAddedAt())->totalHours >= self::MAX_EDITION_HOURS) {
            throw new RuntimeException('Comment can only be edited within 2 hours of being added');
        }

        $this->editor->editComment($this);
    }

    public function getEditor(): User
    {
        return $this->editor;
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
