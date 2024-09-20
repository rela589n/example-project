<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Event;

use App\EmployeePortal\Authentication\Domain\User\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\Domain\User\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Reset\UserPasswordResetEvent;

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
