<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\GetMy\Port\Api;

use App\EmployeePortal\Blog\Post\Features\GetMy\Port\GetMyPostsQuery;
use App\EmployeePortal\Blog\Post\PostCollection;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[ApiDoc\Get(
    summary: 'Get my posts',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Success',
)]
#[AsController]
final readonly class GetMyPostsFrontendApiPoint
{
    public function __construct(
        private PostCollection $postCollection,
    ) {
    }

    #[Route(
        path: '/posts/my',
        name: 'example_project_post_get_my',
        methods: ['GET'],
    )]
    public function __invoke(
        #[CurrentUser]
        UserInterface $user,
    ): Response {
        $query = new GetMyPostsQuery($user->getUserIdentifier());

        $results = $query->execute($this->postCollection);

        return new JsonResponse($results);
    }
}
