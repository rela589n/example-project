<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\ResetPassword\Reset;

use App\FrontPortal\AuthBundle\Domain\User\ResetPassword\PasswordResetRequest;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final readonly class UserPasswordResetEvent implements UserEvent
{
    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        #[ORM\ManyToOne]
        private PasswordResetRequest $passwordResetRequest,
        #[ORM\Column(type: 'carbon_immutable')]
        private CarbonImmutable $timestamp,
    ) {
    }

    public function __invoke(): self
    {
        $this->user->resetPassword($this);

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPasswordResetRequest(): PasswordResetRequest
    {
        return $this->passwordResetRequest;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }
}
