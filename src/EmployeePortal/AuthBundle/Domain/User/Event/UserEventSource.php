<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\Event;

use App\EmployeePortal\AuthBundle\Domain\User\Login\UserLoggedInEvent;
use App\EmployeePortal\AuthBundle\Domain\User\Register\UserRegisteredEvent;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Create\UserPasswordResetRequestCreatedEvent;
use App\EmployeePortal\AuthBundle\Domain\User\ResetPassword\Reset\UserPasswordResetEvent;
use App\Support\Contracts\Auth\Login\UserLoggedInServiceEvent;
use App\Support\Contracts\Auth\Register\UserRegisteredServiceEvent;
use App\Support\Contracts\Auth\ResetPassword\Create\UserPasswordResetRequestCreatedServiceEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/** @implements UserEventVisitor<?object, null> */
#[AsMessageHandler(bus: 'event.bus')]
final readonly class UserEventSource implements UserEventVisitor
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
