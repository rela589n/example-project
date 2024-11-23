<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Event;

use App\EmployeePortal\Authentication\Domain\User\Login\UserLoggedInEvent;
use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegistrationEvent;
use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Reset\UserPasswordResetEvent;
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

    public function visitUserRegisteredEvent(UserRegistrationEvent $event, mixed $data = null): UserRegisteredServiceEvent
    {
        return new UserRegisteredServiceEvent($event->getUser()->getId(), $event->getEmail()->getEmail());
    }

    public function visitUserLoggedInEvent(UserLoggedInEvent $event, mixed $data = null): UserLoggedInServiceEvent
    {
        return new UserLoggedInServiceEvent($event->getUser()->getId());
    }

    public function visitUserPasswordResetRequestCreatedEvent(UserPasswordResetRequestCreatedEvent $event, mixed $data = null): UserPasswordResetRequestCreatedServiceEvent
    {
        return new UserPasswordResetRequestCreatedServiceEvent($event->getUser()->getId(), $event->getPasswordResetRequest()->getId());
    }

    public function visitUserPasswordResetEvent(UserPasswordResetEvent $event, mixed $data = null): null
    {
        return null;
    }
}
