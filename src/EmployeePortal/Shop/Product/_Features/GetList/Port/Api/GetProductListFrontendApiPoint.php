<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\GetList\Port\Api;

use App\EmployeePortal\Shop\Product\_Features\GetList\Port\GetProductListQuery;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Get(
    summary: 'Get Product List',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Products',
)]
#[AsController]
final readonly class GetProductListFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/products',
        name: 'example_project_products_get_list',
        methods: ['GET'],
    )]
    public function __invoke(#[MapQueryString] GetProductListQuery $query, Request $request): Response
    {
        $this->apiBus->dispatch($query, [new PassThroughBusStamp('query.bus')]);

        return new JsonResponse($query->getProducts());
    }
}
