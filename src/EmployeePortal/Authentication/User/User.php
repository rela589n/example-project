<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User;

use App\EmployeePortal\Authentication\User\Email\Email;
use App\EmployeePortal\Authentication\User\Features\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\Password\Password;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset\UserResetPasswordEvent;
use App\EmployeePortal\Authentication\User\Support\Event\UserEvent;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Embedded(columnPrefix: false)]
    private Email $email;

    #[ORM\Embedded]
    private Password $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $updatedAt;

    /** @var Collection<string,UserEvent> */
    #[ORM\OneToMany(targetEntity: UserEvent::class, mappedBy: 'user', cascade: ['persist'], indexBy: 'id')]
    private Collection $events;

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
        $this->events = new ArrayCollection([$event]);
    }

    public function login(UserLoggedInEvent $event): void
    {
        $this->updatedAt = $event->getTimestamp();

        $this->events->add($event);
    }

    public function resetPassword(UserResetPasswordEvent $event): void
    {
        $this->updatedAt = $event->getTimestamp();

        $this->events->add($event);
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

    /** @return Collection<string,UserEvent> */
    public function getEvents(): Collection
    {
        return $this->events;
    }
}
