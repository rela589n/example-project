<?php

declare(strict_types=1);

namespace EmployeePortal\Authentication\Domain\User\Support\Event;

use App\EmployeePortal\Authentication\Domain\User\Actions\Register\Model\UserRegisteredEvent;
use EmployeePortal\Authentication\Domain\User\Actions\Login\UserLoggedInEvent;
use EmployeePortal\Authentication\Domain\User\PasswordReset\Actions\Create\UserPasswordResetRequestCreatedEvent;
use EmployeePortal\Authentication\Domain\User\PasswordReset\Actions\Reset\Model\UserPasswordResetEvent;

/**
 * @template TResult
 * @template TData
 */
interface UserEventVisitor
{
    /**
     * @param null|TData $data
     *
     * @return TResult
     */
    public function visitUserRegisteredEvent(UserRegisteredEvent $event, mixed $data = null): mixed;

    /**
     * @param null|TData $data
     *
     * @return TResult
     */
    public function visitUserPasswordResetEvent(UserPasswordResetEvent $event, mixed $data = null): mixed;

    /**
     * @param null|TData $data
     *
     * @return TResult
     */
    public function visitUserLoggedInEvent(UserLoggedInEvent $event, mixed $data = null): mixed;

    /**
     * @param null|TData $data
     *
     * @return TResult
     */
    public function visitUserPasswordResetRequestCreatedEvent(UserPasswordResetRequestCreatedEvent $event, mixed $data = null): mixed;
}
