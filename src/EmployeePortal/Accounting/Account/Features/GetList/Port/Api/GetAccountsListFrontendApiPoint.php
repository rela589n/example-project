<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\GetList\Port\Api;

use App\EmployeePortal\Accounting\Account\Features\GetList\Port\GetAccountsListQuery;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Get(
    summary: 'Get accounts'
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Success'
)]
#[AsController]
final readonly class GetAccountsListFrontendApiPoint
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(
        path: '/accounts',
        name: 'example_project_account_get_list',
        methods: ['GET'],
    )]
    public function __invoke(
        #[MapQueryString]
        GetAccountsListQuery $query,
    ): Response {
        $results = $query->execute($this->entityManager);

        return new JsonResponse($results);
    }
}
