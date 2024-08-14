<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register;

use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final readonly class UserRegisteredEvent implements UserEvent
{
    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        private Email $email,
        private Password $password,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function process(): void
    {
        $this->user->register($this);
    }
}
