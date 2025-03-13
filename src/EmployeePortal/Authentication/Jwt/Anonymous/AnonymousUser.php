<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Anonymous;

use Override;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AnonymousUser implements UserInterface, EquatableInterface, LegacyPasswordAuthenticatedUserInterface
{
    public const USERNAME = 'anonymous';

    #[Override]
    public function getRoles(): array
    {
        return ['ROLE_ANONYMOUS'];
    }

    #[Override]
    public function getPassword(): ?string
    {
        return null;
    }

    #[Override]
    public function getSalt(): ?string
    {
        return null;
    }

    #[Override]
    public function eraseCredentials(): void
    {
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return self::USERNAME;
    }

    #[Override]
    public function isEqualTo(UserInterface $user): bool
    {
        return $user->getUserIdentifier() === $this->getUserIdentifier();
    }
}
