<?php

declare(strict_types=1);

namespace App\Support\Contracts\EmployeePortal\Authentication\Register;

use Symfony\Component\Uid\Uuid;

final readonly class UserRegisteredServiceEvent
{
    public function __construct(
        private Uuid $userId,
        private string $email,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
