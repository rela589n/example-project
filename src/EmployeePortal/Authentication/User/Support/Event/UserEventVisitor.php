<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Event;

use App\EmployeePortal\Authentication\User\Features\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset\UserResetPasswordEvent;

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
    public function visitUserPasswordResetEvent(UserResetPasswordEvent $event, mixed $data = null): mixed;

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
