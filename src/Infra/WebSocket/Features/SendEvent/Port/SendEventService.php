<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\SendEvent\Port;

use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'ws.event.bus')]
#[AsAlias('app_ws.event.publish.handler')]
final readonly class SendEventService
{
    public function __construct(
        #[Autowire('@app_ws.centrifugo')]
        private(set) CentrifugoInterface $centrifugo,
    ) {
    }

    public function __invoke(SendEventCommand $command): void
    {
        $command->send($this);
    }
}
