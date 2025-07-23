<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\TransferOwnership\Port\Api;

use App\EmployeePortal\Blog\Post\Features\TransferOwnership\Port\TransferPostOwnershipCommand;
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

#[ApiDoc\Post(
    summary: 'Transfer post ownership',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Ownership transferred',
)]
#[AsController]
final readonly class TransferPostOwnershipFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/posts/{id}/transfer-ownership',
        name: 'example_project_blog_post_transfer_ownership',
        methods: ['POST'],
    )]
    public function __invoke(
        string $id,
        Request $request,
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $newOwnerId = $request->getPayload()->getString('newOwnerId');

        $command = new TransferPostOwnershipCommand(
            $id,
            $user->getUserIdentifier(),
            $newOwnerId,
        );

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_OK);
    }
}
