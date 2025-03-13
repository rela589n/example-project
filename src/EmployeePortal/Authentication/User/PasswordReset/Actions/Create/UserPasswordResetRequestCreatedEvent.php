<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Actions\Create;

use App\EmployeePortal\Authentication\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\User\Support\Event\UserEvent;
use App\EmployeePortal\Authentication\User\Support\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\User\User;
use Carbon\CarbonImmutable;
use Closure;
use Doctrine\ORM\Mapping as ORM;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class UserPasswordResetRequestCreatedEvent extends UserEvent
{
    protected const string TYPE = 'userPasswordResetRequestCreated';

    private function __construct(
        protected Uuid $id,
        protected User $user,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private readonly PasswordResetRequest $passwordResetRequest,
        protected CarbonImmutable $timestamp,
    ) {
    }

    public static function process(ClockInterface $clock): Closure
    {
        return static function (User $user, Uuid $id) use ($clock) {
            $event = new self($user, new PasswordResetRequest($id), CarbonImmutable::instance($clock->now()));

            $event->run();

            return $event;
        };
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPasswordResetRequest(): PasswordResetRequest
    {
        return $this->passwordResetRequest;
    }

    private function run(): void
    {
        $this->passwordResetRequest->create($this);
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserPasswordResetRequestCreatedEvent($this, $data);
    }
}
