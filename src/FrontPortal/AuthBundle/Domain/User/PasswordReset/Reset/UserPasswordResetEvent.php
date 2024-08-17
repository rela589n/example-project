<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\PasswordReset\Reset;

use App\FrontPortal\AuthBundle\Domain\User\PasswordReset\PasswordResetRequestRepository;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final readonly class UserPasswordResetEvent implements UserEvent
{
    private function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        #[ORM\ManyToOne]
        private PasswordResetRequestRepository $passwordResetRequest,
        #[ORM\Column(type: 'carbon_immutable')]
        private CarbonImmutable $timestamp,
    ) {
    }

    public static function process(
        User $user,
        PasswordResetRequestRepository $passwordResetRequest,
        DateTimeInterface $timestamp,
    ): self {
        $event = new self($user, $passwordResetRequest, CarbonImmutable::instance($timestamp));

        $event->apply();

        return $event;
    }

    private function apply(): void
    {
        $this->user->resetPassword($this);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPasswordResetRequest(): PasswordResetRequestRepository
    {
        return $this->passwordResetRequest;
    }

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
    }
}
