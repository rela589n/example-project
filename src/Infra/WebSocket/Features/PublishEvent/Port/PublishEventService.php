<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\PublishEvent\Port;

use App\Infra\WebSocket\Features\PublishEvent\UserWebSocketEvent;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'ws.event.bus')]
#[AsAlias('app_ws.event.publish.handler')]
final readonly class PublishEventService
{
    public function __construct(
        #[Autowire('@app_ws.centrifugo')]
        private CentrifugoInterface $centrifugo,
    ) {
    }

    public function __invoke(UserWebSocketEvent $command): void
    {
        $this->centrifugo->publish(
            $command->getPayload(),
            $command->getChannelName(),
        );
    }
}
