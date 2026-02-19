<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Update\Port\Api;

use App\EmployeePortal\Entity\Entity\_Features\Update\Port\UpdateEntityCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[ApiDoc\Put(
    summary: 'Update Entity',
)]
#[ApiDoc\Response(
    response: Response::HTTP_OK,
    description: 'OK',
)]
#[AsController]
final readonly class UpdateEntityFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/entities/{id}',
        name: 'example_project_entity_update',
        methods: ['PUT'],
    )]
    public function __invoke(Uuid $id): Response
    {
        $command = new UpdateEntityCommand($id);

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_OK);
    }
}
