<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Outbox\WelcomeEmail;

use App\EmployeePortal\Authentication\User\Features\Register\UserRegisteredEvent;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler('event.bus')]
final readonly class SendWelcomeEmailOutbox
{
    public function __construct(
        #[Autowire('@consumer.bus')]
        private MessageBusInterface $consumerBus,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $welcomeEmail = new SendWelcomeEmailCommand($event->getEmail());

        // ideally, outbox transaction should be used instead of DispatchAfterCurrentBusStamp
        $this->consumerBus->dispatch($welcomeEmail, [new DispatchAfterCurrentBusStamp(), new PassThroughBusStamp('default.bus')]);
    }
}
