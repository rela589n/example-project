<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\Create\Port;

use App\EmployeePortal\Accounting\Account\Account;
use App\EmployeePortal\Accounting\Account\Features\Create\AccountCreatedEvent;
use Symfony\Component\Uid\Uuid;

final readonly class CreateAccountCommand
{
    public function __construct(
        private string $id,
        private string $userId,
    ) {
    }

    public function process(CreateAccountService $service): void
    {
        $event = $this->createEvent($service);

        $event->process();

        $service->entityManager->persist($event->getAccount());
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }

    private function createEvent(CreateAccountService $service): AccountCreatedEvent
    {
        return new AccountCreatedEvent(
            Uuid::fromString($this->id),
            new Account(),
            Uuid::fromString($this->userId),
            $service->generateNumber(),
            $service->now(),
        );
    }
}
