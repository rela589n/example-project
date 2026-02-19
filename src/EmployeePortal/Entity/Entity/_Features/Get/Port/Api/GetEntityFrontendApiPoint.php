<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Get\Port\Api;

use App\EmployeePortal\Entity\Entity\_Features\Get\Port\GetEntityQuery;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[ApiDoc\Get(
    summary: 'Get Entity by ID',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'Entity retrieved successfully',
)]
#[ApiDoc\Response(
    response: Response::HTTP_NOT_FOUND,
    description: 'Entity not found',
)]
#[AsController]
final readonly class GetEntityFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/entities/{id}',
        name: 'example_project_entity_get',
        methods: ['GET'],
    )]
    public function __invoke(Uuid $id): Response
    {
        $query = new GetEntityQuery($id);

        $this->apiBus->dispatch($query, [new PassThroughBusStamp('query.bus')]);

        return new JsonResponse($query->getResult(), Response::HTTP_OK);
    }
}
