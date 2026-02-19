<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset;

use App\EmployeePortal\Authentication\User\_Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\_Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\_Support\Exception\AccessDeniedException;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'user_reset_password_events')]
final class UserResetPasswordEvent extends UserEvent
{
    protected const string TYPE = 'passwordReset';

    public function __construct(
        protected Uuid $id,
        protected User $user,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private readonly PasswordResetRequest $passwordResetRequest,
        protected CarbonImmutable $timestamp,
    ) {
    }

    public function process(): void
    {
        if (!$this->passwordResetRequest->isForUser($this->user)) {
            throw new AccessDeniedException($this->user);
        }

        if ($this->passwordResetRequest->isExpired($this->timestamp)) {
            throw new ExpiredPasswordResetRequestException($this->passwordResetRequest);
        }

        $this->user->resetPassword($this);
    }

    public function getPasswordResetRequest(): PasswordResetRequest
    {
        return $this->passwordResetRequest;
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserPasswordResetEvent($this, $data);
    }
}
