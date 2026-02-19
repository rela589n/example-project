<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Delete\Port\Api;

use App\EmployeePortal\Entity\Entity\_Features\Delete\Port\DeleteEntityCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[ApiDoc\Delete(
    summary: 'Delete Entity',
)]
#[ApiDoc\Response(
    response: Response::HTTP_NO_CONTENT,
    description: 'No Content',
)]
#[AsController]
final readonly class DeleteEntityFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/entities/{id}',
        name: 'example_project_entity_delete',
        methods: ['DELETE'],
    )]
    public function __invoke(Uuid $id): Response
    {
        $command = new DeleteEntityCommand($id);

        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new Response(status: Response::HTTP_NO_CONTENT);
    }
}
