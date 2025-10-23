<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Create\Port\Api;

use App\EmployeePortal\Voucher\Voucher\Features\Create\Port\CreateVoucherCommand;
use App\Support\MessageBus\PassThrough\PassThroughBusStamp;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[ApiDoc\Post(
    summary: 'Create Voucher',
)]
#[ApiDoc\Response(
    response: Response::HTTP_CREATED,
    description: 'Created',
)]
#[AsController]
final readonly class CreateVoucherFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/generate',
        name: 'example_project_voucher_create',
        methods: ['POST'],
    )]
    public function __invoke(
        Request $request,
        #[MapRequestPayload]
        CreateVoucherCommand $command,
    ): Response {
        $envelope = $this->apiBus->dispatch($command, [new PassThroughBusStamp('command.bus')]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();

        return $response ?? new JsonResponse(['code' => $command->id->toBase58()], status: Response::HTTP_CREATED);
    }
}
