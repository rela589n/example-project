<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\PublishEvent\Port\Api;

use App\Infra\WebSocket\Features\PublishEvent\UserWebSocketEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

use function in_array;
use function random_int;

#[AsController]
final readonly class PublishTestEventFrontendApiPoint
{
    private const TEST_USERS_WHITELIST = [];

    public function __construct(
        #[Autowire('@ws.event.bus')]
        private MessageBusInterface $wsEventBus,
    ) {
    }

    #[Route(
        path: '/centrifugo/test-event',
        name: 'api_frontend_publish_centrifugo_test_event',
        methods: [Request::METHOD_POST]
    )]
    public function __invoke(UserInterface $user): Response
    {
        if (!in_array($user->getUserIdentifier(), self::TEST_USERS_WHITELIST, true)) {
            throw new NotFoundHttpException();
        }

        $userId = Uuid::fromString($user->getUserIdentifier());

        $event = new UserWebSocketEvent($userId, 'test_event', ['test' => 'test event'.random_int(0, 100)]);

        $this->wsEventBus->dispatch($event);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
