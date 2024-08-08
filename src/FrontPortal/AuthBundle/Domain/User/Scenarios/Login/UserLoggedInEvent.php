<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final readonly class UserLoggedInEvent implements UserEvent
{
    public function __construct(
        private User $user,
        #[SensitiveParameter]
        private string $password,
    ) {
    }

    public function process(
        #[Autowire('some-password-hasher')]
        PasswordHasherInterface $passwordHasher,
    ): void {
        $this->user->processLoggedInEvent($this, $passwordHasher);
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
