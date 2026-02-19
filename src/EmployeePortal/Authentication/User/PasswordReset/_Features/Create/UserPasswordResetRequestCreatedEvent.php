<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\_Features\Create;

use App\EmployeePortal\Authentication\User\_Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\_Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class UserPasswordResetRequestCreatedEvent extends UserEvent
{
    protected const string TYPE = 'passwordResetRequestCreated';

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
        $this->passwordResetRequest->create($this);
    }

    public function getPasswordResetRequest(): PasswordResetRequest
    {
        return $this->passwordResetRequest;
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserPasswordResetRequestCreatedEvent($this, $data);
    }
}
