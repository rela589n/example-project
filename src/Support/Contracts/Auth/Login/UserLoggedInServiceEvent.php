<?php

declare(strict_types=1);

namespace App\Support\Contracts\Auth\Login;

use Symfony\Component\Uid\Uuid;

final readonly class UserLoggedInServiceEvent
{
    public function __construct(
        private Uuid $userId,
    ) {
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
