<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User;

use App\EmployeePortal\Authentication\User\Actions\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Actions\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Password\Password;
use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\UserPasswordResetEvent;
use App\EmployeePortal\Authentication\User\Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\Support\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Cycle\Annotated\Annotation as Cycle;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Cycle\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Embedded(columnPrefix: false)]
    private Email $email;

    #[ORM\Embedded]
    private Password $password;

    #[ORM\Column(type: 'carbon_immutable')]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: 'carbon_immutable')]
    private CarbonImmutable $updatedAt;

    /** @var UserEvent[] */
    #[ORM\OneToMany(targetEntity: UserEvent::class, mappedBy: 'user')]
    private array $events = [];

    /**
     * It is a lot much easier to update the entity from the event object in one call (e.g. user.register())
     * rather than in a bunch of anemic setters called (e.g. user.setEmail, user.setPassword)
     */
    public function register(UserRegisteredEvent $event): void
    {
        $this->id = $event->getId();
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
        $this->createdAt = $event->getTimestamp();
        $this->updatedAt = $event->getTimestamp();
        $this->events[] = $event;
    }

    public function login(UserLoggedInEvent $event): void
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
