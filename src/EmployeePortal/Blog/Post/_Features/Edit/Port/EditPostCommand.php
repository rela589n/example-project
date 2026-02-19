<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\Edit\Port;

use App\EmployeePortal\Blog\Post\_Features\Edit\PostEditedEvent;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class EditPostCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $id,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $editorId,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        private string $title,
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 2047)]
        private string $description,
    ) {
    }

    public function process(EditPostService $service): void
    {
        $violationList = $service->validator->validate($this);

        if (0 !== $violationList->count()) {
            throw new ValidationFailedException($this, $violationList);
        }

        $editor = $service->userCollection->get(Uuid::fromString($this->editorId));
        $post = $service->postCollection->get(Uuid::fromString($this->id));

        $event = new PostEditedEvent(
            $editor,
            $post,
            $this->title,
            $this->description,
        );

        $event->process();

        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEditorId(): string
    {
        return $this->editorId;
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
