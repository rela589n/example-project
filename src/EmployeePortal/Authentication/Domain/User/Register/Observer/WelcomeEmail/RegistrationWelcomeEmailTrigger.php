<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Observer\WelcomeEmail;

use App\EmployeePortal\Authentication\Domain\User\Register\Model\UserRegistrationEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler('event.bus')]
final readonly class RegistrationWelcomeEmailTrigger
{
    public function __construct(
        #[Autowire('@consumer.bus')]
        private MessageBusInterface $consumerBus,
    ) {
    }

    public function __invoke(UserRegistrationEvent $event): void
    {
        $welcomeEmail = new RegistrationWelcomeEmail($event->getEmail());

        $this->consumerBus->dispatch($welcomeEmail, [new DispatchAfterCurrentBusStamp()]);
    }
}
