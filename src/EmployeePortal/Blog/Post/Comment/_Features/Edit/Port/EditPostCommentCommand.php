<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\_Features\Edit\Port;

use App\EmployeePortal\Blog\Post\Comment\_Features\Edit\PostCommentEditedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class EditPostCommentCommand
{
    public function __construct(
        private string $userId,
        private string $postId,
        private string $commentId,
        private string $text,
    ) {
    }

    public function process(EditPostCommentService $service): void
    {
        $user = $service->userCollection->get(Uuid::fromString($this->userId));
        $post = $service->postCollection->get(Uuid::fromString($this->postId));

        $event = new PostCommentEditedEvent(
            $user,
            $post,
            Uuid::fromString($this->commentId),
            $this->text,
            $service->now(),
        );

        $event->process();

        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
