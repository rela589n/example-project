<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\Create\Port;

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
        $event = new AccountCreatedEvent(
            Uuid::fromString($this->id),
            Uuid::fromString($this->userId),
            $service->generateNumber(),
            $service->now(),
        );

        $account = $event->process();

        $service->entityManager->persist($account);
        $service->entityManager->flush();

        $service->eventBus->dispatch($event);
    }
}
