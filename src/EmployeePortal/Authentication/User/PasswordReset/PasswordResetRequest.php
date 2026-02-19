<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset;

use App\EmployeePortal\Authentication\User\PasswordReset\_Features\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Repository\PasswordResetRequestRepository;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PasswordResetRequestRepository::class)]
#[ORM\Table(name: 'user_password_reset_requests')]
class PasswordResetRequest
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private CarbonImmutable $expiresAt;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function create(UserPasswordResetRequestCreatedEvent $event): void
    {
        $this->id = $event->getId();
        $this->user = $event->getUser();
        $this->createdAt = $event->getTimestamp();
        $this->expiresAt = $event->getTimestamp()->addMinutes(10);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function isForUser(User $user): bool
    {
        return $this->user === $user;
    }

    public function isExpired(CarbonImmutable $now): bool
    {
        return $this->expiresAt->isBefore($now);
    }
}
