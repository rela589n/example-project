<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\User;

use Fresh\CentrifugoBundle\User\CentrifugoUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class CentrifugoUser implements CentrifugoUserInterface
{
    public function __construct(
        private UserInterface $user,
    ) {
    }

    public function getCentrifugoSubject(): string
    {
        return $this->user->getUserIdentifier();
    }

    public function getCentrifugoUserInfo(): array
    {
        return [];
    }
}
