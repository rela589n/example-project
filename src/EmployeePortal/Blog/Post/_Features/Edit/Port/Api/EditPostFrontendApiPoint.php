<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\Edit\Port\Api;

use App\EmployeePortal\Blog\Post\_Features\Edit\Port\EditPostCommand;
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

#[ApiDoc\Put(
    summary: 'Edit post',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Updated',
)]
#[AsController]
final readonly class EditPostFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/posts/{id}',
        name: 'example_project_blog_post_edit',
        methods: ['PUT'],
    )]
    public function __invoke(
        string $id,
        Request $request,
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $title = $request->getPayload()->getString('title');
        $description = $request->getPayload()->getString('description');

        $command = new EditPostCommand(
            $id,
            $user->getUserIdentifier(),
            $title,
            $description,
        );

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_OK);
    }
}
