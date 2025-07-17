<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\TransferOwnership\Port;

use App\EmployeePortal\Blog\Post\Features\TransferOwnership\PostOwnershipTransferredEvent;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class TransferPostOwnershipCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $postId,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $currentOwnerId,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        private string $newOwnerId,
    ) {
    }

    public function process(TransferPostOwnershipService $service): void
    {
        $violationList = $service->validator->validate($this);

        if (0 !== $violationList->count()) {
            throw new ValidationFailedException($this, $violationList);
        }

        $post = $service->postCollection->get(Uuid::fromString($this->postId));
        $currentOwner = $service->userCollection->get(Uuid::fromString($this->currentOwnerId));
        $newOwner = $service->userCollection->get(Uuid::fromString($this->newOwnerId));

        $event = new PostOwnershipTransferredEvent(
            $currentOwner,
            $newOwner,
            $post,
        );

        $event->process();

        $service->entityManager->flush();
        $service->eventBus->dispatch($event);
    }
}
