<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword;

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
        private CarbonImmutable $dateTime,
    ) {
    }

    public static function of(Future $user, Future $passwordResetRequest): self
    {

    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPasswordResetRequest(): PasswordResetRequest
    {
        return $this->passwordResetRequest;
    }

    public function getDateTime(): CarbonImmutable
    {
        return $this->dateTime;
    }

    public function process(): void
    {
        $this->user->processPasswordResetEvent($this);
    }
}
