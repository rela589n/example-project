<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\PasswordReset\Features\Create\Port\Api;

use App\EmployeePortal\Authentication\User\Features\Login\Port\LoginUserCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class CreatePasswordRequestResetApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }
    #[Route(
        path: '/password-requests',
        name: 'example_project_auth_password_request_create',
        methods: ['POST'],
    )]
    public function __invoke(
        #[MapRequestPayload]
        LoginUserCommand $command,
    ): Response {
        $envelope = $this->apiBus->dispatch($command, [/*new BusNameStamp('command.bus')*/]);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $response */
        $response = $handled->getResult();
        $response ??= $this->authenticationSuccessHandler->handleAuthenticationSuccess($command->getJwtUser(), $command->getJwtToken());

        return $response;
    }
}
