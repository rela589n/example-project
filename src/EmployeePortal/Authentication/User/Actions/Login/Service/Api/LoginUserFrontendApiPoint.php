<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Login\Service\Api;

use App\EmployeePortal\Authentication\User\Actions\Login\Service\LoginUserCommand;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final readonly class LoginUserFrontendApiPoint
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
    ) {
    }

    #[Route(
        path: '/register',
        name: 'employee_portal_auth_user_register',
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
