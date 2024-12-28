<?php

declare(strict_types=1);

namespace EmployeePortal\Authentication\Domain\User\PasswordReset\Actions\Create;

use App\EmployeePortal\Authentication\Domain\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\Domain\User\User;
use Carbon\CarbonImmutable;
use Closure;
use Doctrine\ORM\Mapping as ORM;
use EmployeePortal\Authentication\Domain\User\Support\Event\UserEvent;
use EmployeePortal\Authentication\Domain\User\Support\Event\UserEventVisitor;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class UserPasswordResetRequestCreatedEvent implements UserEvent
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

    public function getTimestamp(): CarbonImmutable
    {
        return $this->timestamp;
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
