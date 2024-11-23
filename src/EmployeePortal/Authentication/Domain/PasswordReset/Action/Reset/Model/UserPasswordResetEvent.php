<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Model;

use App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Model\Exception\ExpiredPasswordResetRequestException;
use App\EmployeePortal\Authentication\Domain\User\PasswordReset\PasswordResetRequest;
use App\EmployeePortal\Authentication\Domain\User\User;
use App\EmployeePortal\Authentication\Domain\User\User\Event\UserEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Event\UserEventVisitor;
use App\EmployeePortal\Authentication\Domain\User\User\Exception\AccessDeniedException;
use Carbon\CarbonImmutable;
use Closure;
use Doctrine\ORM\Mapping as ORM;
use Psr\Clock\ClockInterface;

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

    /** @return Closure(User $user, PasswordResetRequest $passwordResetRequest): self */
    public static function process(ClockInterface $clock): Closure
    {
        return static function (User $user, PasswordResetRequest $passwordResetRequest) use ($clock) {
            $event = new self($user, $passwordResetRequest, CarbonImmutable::instance($clock->now()));

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
        if (!$this->passwordResetRequest->isForUser($this->user)) {
            throw new AccessDeniedException($this->user);
        }

        if ($this->passwordResetRequest->isExpired($this->timestamp)) {
            throw new ExpiredPasswordResetRequestException($this->passwordResetRequest);
        }

        $this->user->resetPassword($this);
    }

    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed
    {
        return $visitor->visitUserPasswordResetEvent($this, $data);
    }
}
