<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;

final readonly class UserLoggedInEvent implements UserEvent
{
    public function __construct(
        private User $user,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function process(): void
    {
        $this->user->processLoggedInEvent($this);
    }

    public function verifyPassword()
    {
        
    }
}
