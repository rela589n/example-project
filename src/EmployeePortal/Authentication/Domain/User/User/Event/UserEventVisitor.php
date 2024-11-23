<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\User\Event;

use App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Model\UserPasswordResetEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\Domain\User\User\Action\Register\UserRegistrationEvent;

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
    public function visitUserRegisteredEvent(UserRegistrationEvent $event, mixed $data = null): mixed;

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
