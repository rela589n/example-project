<?php

declare(strict_types=1);

namespace EmployeePortal\Authentication\Domain\User\Support\Event;

use App\EmployeePortal\Authentication\Domain\AuthEvent;

/**
 * If in your case it's necessary to implement command-side replica,
 * you could create a single event listener for UserEvent and dispatch every and all
 * user events to other microservices so that they could update their state as well.
 *
 * In the simplest case, one could on any user event send the actual snapshot of user data
 * so that it's not necessary to treat the events separately.
 */
interface UserEvent extends AuthEvent
{
    /**
     * @template TResult
     * @template TData
     *
     * @param UserEventVisitor<TResult,TData> $visitor
     * @param null|TData $data
     *
     * @return TResult
     */
    public function acceptVisitor(UserEventVisitor $visitor, mixed $data = null): mixed;
}
