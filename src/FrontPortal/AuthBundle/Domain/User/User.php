<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User;

use App\FrontPortal\AuthBundle\Domain\User\Login\UserLoggedInEvent;
use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Reset\UserPasswordResetEvent;
use App\FrontPortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Email\Email;
use App\FrontPortal\AuthBundle\Domain\ValueObject\Password\Password;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    private Uuid $id;

    private Email $email;

    private Password $password;

    private CarbonImmutable $createdAt;

    private CarbonImmutable $updatedAt;

    /** @var UserEvent[] */
    #[ORM\OneToMany(targetEntity: UserEvent::class, mappedBy: 'user')]
    private array $events = [];

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function register(UserRegisteredEvent $event): void
    {
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
        $this->createdAt = $event->getTimestamp();
        $this->events[] = $event;
    }

    public function logIn(UserLoggedInEvent $event): void
    {
        $this->updatedAt = $event->getTimestamp();

        $this->events[] = $event;
    }

    public function resetPassword(UserPasswordResetEvent $event): void
    {
        $this->updatedAt = $event->getTimestamp();

        $this->events[] = $event;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }
}
