<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\Create\Port;

use App\EmployeePortal\Blog\Post\Features\Create\PostCreatedEvent;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class CreatePostCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $id,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $authorId,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        private string $title,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 2047)]
        private string $description,
    ) {
    }

    public function process(CreatePostService $service): void
    {
        $violationList = $service->validator->validate($this);

        if (0 !== $violationList->count()) {
            throw new ValidationFailedException($this, $violationList);
        }

        $author = $service->userCollection->get(Uuid::fromString($this->authorId));

        $event = new PostCreatedEvent(
            Uuid::fromString($this->id),
            $author,
            $this->title,
            $this->description,
        );

        $post = $event->process();

        $service->entityManager->persist($post);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
