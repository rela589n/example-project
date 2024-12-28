<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Event;

use App\EmployeePortal\Authentication\User\Actions\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Actions\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Actions\Reset\UserPasswordResetEvent;

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
