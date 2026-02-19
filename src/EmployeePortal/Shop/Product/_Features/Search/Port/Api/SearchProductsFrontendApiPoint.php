<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Search\Port\Api;

use App\EmployeePortal\Shop\Product\_Features\Search\Port\SearchProductsQuery;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Get(summary: 'Search Products')]
#[ApiDoc\Response(response: Response::HTTP_OK, description: 'Search results')]
#[AsController]
final readonly class SearchProductsFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/products/search',
        name: 'example_project_products_search',
        methods: ['GET'],
    )]
    public function __invoke(#[MapQueryString] SearchProductsQuery $query): Response
    {
        $this->apiBus->dispatch($query, [new PassThroughBusStamp('query.bus')]);

        return new JsonResponse($query->getResults());
    }
}
