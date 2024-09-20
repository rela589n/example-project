<?php

declare(strict_types=1);

namespace App\Support\Contracts\EmployeePortal\Authentication\ResetPassword\Create;

use Symfony\Component\Uid\Uuid;

final readonly class UserPasswordResetRequestCreatedServiceEvent
{
    public function __construct(
        private Uuid $userId,
        private Uuid $passwordResetRequestId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getPasswordResetRequestId(): Uuid
    {
        return $this->passwordResetRequestId;
    }
}
