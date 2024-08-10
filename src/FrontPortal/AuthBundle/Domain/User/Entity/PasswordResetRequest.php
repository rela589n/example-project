<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Entity;

use App\FrontPortal\AuthBundle\Domain\User\User;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

final readonly class PasswordResetRequest
{
    private Uuid $id;

    private User $user;

    private CarbonImmutable $validUntil;

    public function __construct(User $user)
    {
        $this->id = Uuid::v7();
        $this->user = $user;
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
