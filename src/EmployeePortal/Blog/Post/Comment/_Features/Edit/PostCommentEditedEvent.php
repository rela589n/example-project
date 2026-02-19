<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\_Features\Edit;

use App\EmployeePortal\Blog\Post\Comment\PostComment;
use App\EmployeePortal\Blog\Post\Post;
use App\EmployeePortal\Blog\User\User;
use Carbon\CarbonImmutable;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

final readonly class PostCommentEditedEvent
{
    private const MAX_EDITION_HOURS = 2;

    private PostComment $comment;

    public function __construct(
        private User $editor,
        private Post $post,
        private Uuid $commentId,
        private string $text,
        private CarbonImmutable $timestamp,
    ) {
        $this->comment = $this->post->comments->get($this->commentId);
    }

    public function process(): void
    {
        $this->comment->assertIsAuthoredBy($this->editor);

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
