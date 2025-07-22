<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Edit\Port\Api;

use App\EmployeePortal\Blog\Post\Comment\Features\Edit\Port\EditPostCommentCommand;
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
    summary: 'Edit post comment',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'OK',
)]
#[AsController]
final readonly class EditPostCommentFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/posts/{postId}/comments/{commentId}',
        name: 'example_project_post_comment_edit',
        methods: ['PUT'],
    )]
    public function __invoke(
        Request $request,
        string $postId,
        string $commentId,
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $command = new EditPostCommentCommand(
            $user->getUserIdentifier(),
            $postId,
            $commentId,
            $request->getPayload()->get('text'),
        );

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_OK);
    }
}
