<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User;

use App\EmployeePortal\Authentication\Domain\User\Event\UserEvent;
use App\EmployeePortal\Authentication\Domain\User\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegisteredEvent;
use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Reset\UserPasswordResetEvent;
use App\EmployeePortal\Authentication\Domain\ValueObject\Email\Email;
use App\EmployeePortal\Authentication\Domain\ValueObject\Password\Password;
use Carbon\CarbonImmutable;
use Cycle\Annotated\Annotation as Cycle;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Cycle\Entity]
class User
{
    #[Cycle\Column('uuid')]
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

    /**
     * It is a lot much easier to update the entity from the event object in one call (e.g. user.register())
     * rather than in a bunch of anemic setters called (e.g. user.setEmail, user.setPassword)
     */
    public function register(UserRegisteredEvent $event): void
    {
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
        $this->createdAt = $event->getTimestamp();
        $this->updatedAt = $event->getTimestamp();
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

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->updatedAt;
    }
}
