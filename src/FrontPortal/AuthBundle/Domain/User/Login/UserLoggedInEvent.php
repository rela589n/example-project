<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Login;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[ORM\Entity]
final readonly class UserLoggedInEvent implements UserEvent
{
    private function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
    ) {
    }

    public static function process(User $user, string $plainPassword, PasswordHasherInterface $passwordHasher): UserLoggedInEvent
    {
        $event = new self($user);

        $event->user->getPassword()->verify($plainPassword, $passwordHasher);
        $event->apply();

        return $event;
    }

    private function apply(): void
    {
        $this->user->logIn($this);
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
