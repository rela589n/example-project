<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Port\Api;

use App\EmployeePortal\Authentication\User\Features\Register\Port\RegisterUserCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Post(
    summary: 'Register user',
)]
#[ApiDoc\Response(
    response: 201,
    description: 'Registered',
)]
#[AsController]
final readonly class RegisterUserFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/register',
        name: 'example_project_auth_user_register',
        methods: ['POST'],
    )]
    public function __invoke(
        #[MapRequestPayload]
        RegisterUserCommand $command,
    ): Response {
        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_CREATED);
    }
}
