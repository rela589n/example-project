<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Update\Port\Api;

use App\EmployeePortal\Shop\Category\_Features\Update\Port\UpdateCategoryCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[ApiDoc\Put(
    summary: 'Update Category',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'OK',
)]
#[AsController]
final readonly class UpdateCategoryFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/categories/{id}',
        name: 'example_project_category_update',
        methods: ['PUT'],
    )]
    public function __invoke(
        Uuid $id,
        #[MapRequestPayload]
        UpdateCategoryPayload $payload,
    ): Response {
        $command = new UpdateCategoryCommand($id, $payload->name);

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_OK);
    }
}
