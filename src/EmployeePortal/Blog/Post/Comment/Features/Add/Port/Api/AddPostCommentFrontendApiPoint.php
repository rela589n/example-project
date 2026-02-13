<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Add\Port\Api;

use App\EmployeePortal\Blog\Post\Comment\Features\Add\Port\AddPostCommentCommand;
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
    summary: 'Add post comment',
)]
#[ApiDoc\Response(
    response: Response::HTTP_CREATED,
    description: 'Created',
)]
#[AsController]
final readonly class AddPostCommentFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/posts/{postId}/comments',
        name: 'example_project_post_comment_add',
        methods: ['POST'],
    )]
    public function __invoke(
        Request $request,
        string $postId,
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $command = new AddPostCommentCommand(
            $user->getUserIdentifier(),
            $postId,
            $request->getPayload()->getString('text'),
            $request->getPayload()->getString('id') ?: Uuid::v7()->toRfc4122(),
        );

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_CREATED);
    }
}
