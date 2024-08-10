<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword;

use Amp\Future;
use App\FrontPortal\AuthBundle\Domain\User\Entity\PasswordResetRequest;
use App\FrontPortal\AuthBundle\Domain\User\User;
use App\FrontPortal\AuthBundle\Domain\User\UserEvent;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final readonly class UserPasswordResetEvent implements UserEvent
{
    private function __construct(
        #[ORM\ManyToOne(inversedBy: 'events')]
        private User $user,
        #[ORM\ManyToOne]
        private PasswordResetRequest $passwordResetRequest,
        #[ORM\Column(type: 'carbon_immutable')]
        private CarbonImmutable $timestamp,
    ) {
    }

    public static function of(Future $user, Future $passwordResetRequest): self
    {
        [$user, $passwordResetRequest] = Future\awaitAnyN(2, [$user, $passwordResetRequest]);

        return new self($user, $passwordResetRequest, CarbonImmutable::now());
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

    public function process(): void
    {
        $this->user->processPasswordResetEvent($this);
    }
}
