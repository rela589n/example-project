<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\Register\Api;

use App\EmployeePortal\Authentication\Domain\User\Register\Model\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\Authentication\Domain\User\Register\Service\RegisterUserCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Post(
    summary: 'Register user'
)]
#[OA\Response(
    response: 201,
    description: 'Registered',
)]
#[AsController]
final readonly class RegisterUserFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/register',
        name: 'employee_portal_auth_user_register',
        methods: ['POST'],
    )]
    public function __invoke(
        #[MapRequestPayload]
        RegisterUserCommand $command,
    ): Response {
        try {
            $envelope = $this->apiBus->dispatch($command, [/*new BusNameStamp('command.bus')*/]);
        } catch (EmailAlreadyTakenException) {
            // Some domain exceptions (scoped to this particular scenario) could be caught and formatted into the response:

            return new JsonResponse([
                'error' => 'email_already_taken',
                'errorDescription' => 'Email is already taken',
            ], 400);
        }

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $result */
        $result = $handled->getResult();

        return $result ?? new Response(status: 201);
    }
}
