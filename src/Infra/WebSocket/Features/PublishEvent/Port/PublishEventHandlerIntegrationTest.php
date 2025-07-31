<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\PublishEvent\Port;

use App\Infra\WebSocket\Features\PublishEvent\UserWebSocketEvent;
use Fresh\CentrifugoBundle\Service\CentrifugoInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/** @internal */
#[CoversClass(UserWebSocketEvent::class)]
#[CoversClass(PublishEventService::class)]
final class PublishEventHandlerIntegrationTest extends KernelTestCase
{
    private CentrifugoInterface&MockObject $centrifugo;

    private MessageBusInterface $wsBus;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ContainerInterface $container */
        $container = self::getContainer();
        $this->centrifugo = $this->createMock(CentrifugoInterface::class);

        $container->set('app_ws.centrifugo', $this->centrifugo);

        /** @var MessageBusInterface $bus */
        $bus = $container->get('ws.event.bus');
        $this->wsBus = $bus;
    }

    public function testDispatchesUserEventToCentrifugo(): void
    {
        $this->centrifugo
            ->expects(self::once())
            ->method('publish')
            ->with([
                'event' => 'test_event',
                'data' => [
                    'some' => 'payload',
                ],
            ], 'user_events:general#138140ed-1dd2-11b2-a4a6-17edf8bcbc91')
        ;

        $command = new UserWebSocketEvent(
            Uuid::fromString('138140ed-1dd2-11b2-a4a6-17edf8bcbc91'),
            'test_event',
            ['some' => 'payload'],
        );

        $this->wsBus->dispatch($command);
    }
}
