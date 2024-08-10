<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login;

use Amp\Future;
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

    public static function of(Future $user, string $plainPassword, PasswordHasherInterface $passwordHasher): UserLoggedInEvent
    {
        $event = new self($user->await());

        $event->user->verifyPassword($plainPassword, $passwordHasher);

        return $event;
    }

    public function process(): void
    {
        $this->user->processLoggedInEvent($this);
    }
}
