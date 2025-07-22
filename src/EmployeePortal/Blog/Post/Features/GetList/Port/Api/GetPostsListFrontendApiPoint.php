<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\GetList\Port\Api;

use App\EmployeePortal\Blog\Post\Features\GetList\Port\GetPostsListQuery;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Get(
    summary: 'Get posts',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Success',
)]
#[AsController]
final readonly class GetPostsListFrontendApiPoint
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(
        path: '/posts',
        name: 'example_project_post_get_list',
        methods: ['GET'],
    )]
    public function __invoke(
        #[MapQueryString]
        GetPostsListQuery $query,
    ): Response {
        $results = $query->execute($this->entityManager);

        return new JsonResponse($results);
    }
}
