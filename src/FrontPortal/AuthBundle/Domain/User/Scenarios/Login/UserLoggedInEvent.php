<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use Closure;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final readonly class UserLoggedInEvent implements UserEvent
{
    private function __construct(
        private User $user,
    ) {
    }

    public static function of(Closure $user, string $plainPassword, PasswordHasherInterface $passwordHasher): UserLoggedInEvent
    {
        $event = new self($user());

        $event->user->verifyPassword($plainPassword, $passwordHasher);

        return $event;
    }

    public function process(): void
    {
        $this->user->processLoggedInEvent($this);
    }
}
