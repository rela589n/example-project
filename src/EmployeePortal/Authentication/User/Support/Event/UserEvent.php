<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Event;

use App\EmployeePortal\Authentication\User\Features\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset\UserResetPasswordEvent;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * If in your case it's necessary to implement command-side replica,
 * you could create a single event listener for UserEvent and dispatch every and all
 * user events to other microservices so that they could update their state as well.
 *
 * In the simplest case, one could on any user event send the actual snapshot of user data
 * so that it's not necessary to treat the events separately.
 */
#[ORM\Entity]
#[ORM\Table(name: 'user_events')]
#[ORM\InheritanceType(value: 'JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(value: [
    UserRegisteredEvent::TYPE => UserRegisteredEvent::class,
    UserLoggedInEvent::TYPE => UserLoggedInEvent::class,
    UserPasswordResetRequestCreatedEvent::TYPE => UserPasswordResetRequestCreatedEvent::class,
    UserResetPasswordEvent::TYPE => UserResetPasswordEvent::class,
])]
abstract class UserEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    protected Uuid $id;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    protected User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    protected CarbonImmutable $timestamp;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }

    /**
     * @template TResult
     * @template TData
     *
     * @param UserEventVisitor<TResult,TData> $visitor
     * @param TData|null $data
     *
     * @return TResult
     */
    abstract public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed;
}
