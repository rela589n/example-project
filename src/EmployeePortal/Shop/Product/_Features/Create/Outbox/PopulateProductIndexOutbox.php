<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Create\Outbox;

use App\EmployeePortal\Shop\Product\_Features\Create\ProductCreatedEvent;
use App\EmployeePortal\Shop\Product\_Features\Index\Port\IndexProductCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler('event.bus')]
final readonly class PopulateProductIndexOutbox
{
    public function __construct(
        #[Autowire('@consumer.bus')]
        private MessageBusInterface $consumerBus,
    ) {
    }

    public function __invoke(ProductCreatedEvent $event): void
    {
        $indexProductCommand = new IndexProductCommand($event->id);

        // ideally, outbox transaction should be used instead of DispatchAfterCurrentBusStamp
        $this->consumerBus->dispatch($indexProductCommand, [new DispatchAfterCurrentBusStamp(), new PassThroughBusStamp('command.bus')]);
    }
}
