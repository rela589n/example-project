<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\ResetPassword;

use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\Domain\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PasswordResetRequestRepository::class)]
final readonly class PasswordResetRequest
{
    private Uuid $id;

    private User $user;

    private CarbonImmutable $createdAt;

    private CarbonImmutable $validUntil;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    public function create(UserPasswordResetRequestCreatedEvent $event): void
    {
        $this->user = $event->getUser();
        $this->createdAt = $event->getTimestamp();
        $this->validUntil = $event->getTimestamp()->addMinutes(10);
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
        return $this->validUntil->isBefore($now);
    }
}
