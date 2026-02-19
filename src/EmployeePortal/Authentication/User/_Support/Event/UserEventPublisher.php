<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\_Support\Event;

use App\EmployeePortal\Authentication\User\Features\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\User\PasswordReset\Features\Reset\UserResetPasswordEvent;
use App\Support\Contracts\EmployeePortal\Authentication\Login\UserLoggedInServiceEvent;
use App\Support\Contracts\EmployeePortal\Authentication\Register\UserRegisteredServiceEvent;
use App\Support\Contracts\EmployeePortal\Authentication\ResetPassword\Create\UserPasswordResetRequestCreatedServiceEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @implements UserEventVisitor<?object, null> */
#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserEventPublisher implements UserEventVisitor
{
    public function __construct(
        #[Autowire('@service.event.bus')]
        private MessageBusInterface $serviceEventBus,
    ) {
    }

    public function __invoke(UserEvent $event): void
    {
        $serviceEvent = $event->acceptVisitor($this);

        if (null === $serviceEvent) {
            return;
        }

        $this->serviceEventBus->dispatch($serviceEvent);
    }

    public function visitUserRegisteredEvent(UserRegisteredEvent $event, mixed $data = null): UserRegisteredServiceEvent
    {
        return new UserRegisteredServiceEvent($event->getUser()->getId(), $event->getEmail()->toString());
    }

    public function visitUserLoggedInEvent(UserLoggedInEvent $event, mixed $data = null): UserLoggedInServiceEvent
    {
        return new UserLoggedInServiceEvent($event->getUser()->getId());
    }

    public function visitUserPasswordResetRequestCreatedEvent(UserPasswordResetRequestCreatedEvent $event, mixed $data = null): UserPasswordResetRequestCreatedServiceEvent
    {
        return new UserPasswordResetRequestCreatedServiceEvent($event->getUser()->getId(), $event->getPasswordResetRequest()->getId());
    }

    public function visitUserPasswordResetEvent(UserResetPasswordEvent $event, mixed $data = null): null
    {
        return null;
    }
}
