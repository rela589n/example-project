<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Exception\PasswordMismatchException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class User
{
    private Uuid $id;

    private Email $email;

    private Password $password;

    /** @var UserEvent[] */
    private array $events = [];

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function processRegisteredEvent(UserRegisteredEvent $event): void
    {
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
        $this->events[] = $event;
    }

    public function processLoggedInEvent(UserLoggedInEvent $event): void
    {
        $this->events[] = $event;
    }

    public function verifyPassword(string $plainPassword, PasswordHasherInterface $passwordHasher): void
    {
        if (!$passwordHasher->verify($this->password->getHash(), $plainPassword)) {
            throw new PasswordMismatchException($plainPassword);
        }
    }
}
