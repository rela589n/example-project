<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Add\Port;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\PostCommentAddedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class AddPostCommentCommand
{
    public function __construct(
        private string $userId,
        private string $postId,
        private string $text,
        private string $id,
    ) {
    }

    public function process(AddPostCommentService $service): void
    {
        $user = $service->userCollection->get(Uuid::fromString($this->userId));
        $post = $service->postCollection->get(Uuid::fromString($this->postId));

        $id = $this->id ? Uuid::fromString($this->id) : Uuid::v7();

        $event = new PostCommentAddedEvent(
            $id,
            $user,
            $post,
            $this->text,
            $service->now(),
        );

        $comment = $event->process();

        $service->entityManager->persist($comment);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
