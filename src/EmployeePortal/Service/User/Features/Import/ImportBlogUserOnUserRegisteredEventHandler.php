<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\User\Features\Import;

use App\EmployeePortal\Service\User\User;
use App\Support\Contracts\EmployeePortal\Authentication\Register\UserRegisteredServiceEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler('service.event.bus')]
final readonly class ImportBlogUserOnUserRegisteredEventHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UserRegisteredServiceEvent $event): void
    {
        $user = new User($event->getUserId());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
