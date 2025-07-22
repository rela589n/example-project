<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\Create\Port\Api;

use App\EmployeePortal\Accounting\Account\Features\Create\Port\CreateAccountCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

#[ApiDoc\Post(
    summary: 'Create account',
)]
#[ApiDoc\Response(
    response: Response::HTTP_CREATED,
    description: 'Created',
)]
#[AsController]
final readonly class CreateAccountFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/account',
        name: 'example_project_account_create',
        methods: ['POST'],
    )]
    public function __invoke(
        Request $request,
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $id = $request->getPayload()->get('id');

        $command = new CreateAccountCommand(
            $id ?? Uuid::v7()->toRfc4122(),
            $user->getUserIdentifier(),
        );

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_CREATED);
    }
}
