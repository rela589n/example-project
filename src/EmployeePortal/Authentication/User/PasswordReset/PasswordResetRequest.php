<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset;

use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Repository\PasswordResetRequestRepository;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PasswordResetRequestRepository::class)]
final readonly class PasswordResetRequest
{
    private Uuid $id;

    private User $user;

    private CarbonImmutable $createdAt;

    private CarbonImmutable $expiresAt;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function create(UserPasswordResetRequestCreatedEvent $event): void
    {
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
