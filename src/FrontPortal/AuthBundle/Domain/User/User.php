<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User;

use App\FrontPortal\AuthBundle\Domain\User\Exception\AccessDeniedException;
use App\FrontPortal\AuthBundle\Domain\User\Exception\ExpiredPasswordResetRequestException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\Exception\PasswordMismatchException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\UserPasswordResetEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class User
{
    private Uuid $id;

    private Email $email;

    private Password $password;

    /** @var UserEvent[] */
    #[ORM\OneToMany(targetEntity: UserEvent::class, mappedBy: 'user')]
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

    public function processPasswordResetEvent(UserPasswordResetEvent $event): void
    {
        $timestamp = $event->getTimestamp();
        $request = $event->getPasswordResetRequest();

        if (!$request->isForUser($this)) {
            throw new AccessDeniedException($this);
        }

        if ($request->isExpired($timestamp)) {
            throw new ExpiredPasswordResetRequestException($request);
        }

        $this->events[] = $event;
    }
}
