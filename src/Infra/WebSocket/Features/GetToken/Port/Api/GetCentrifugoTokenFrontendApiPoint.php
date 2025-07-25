<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\Features\GetToken\Port\Api;

use App\Infra\WebSocket\CentrifugoUser;
use Fresh\CentrifugoBundle\Service\Credentials\CredentialsGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
final readonly class GetCentrifugoTokenFrontendApiPoint
{
    public function __construct(
        private CredentialsGenerator $credentialsGenerator,
    ) {
    }

    #[Route(
        path: '/centrifugo/token',
        name: 'api_frontend_get_centrifugo_token',
        methods: [Request::METHOD_GET]
    )]
    public function __invoke(
        #[Autowire('@=service("security.token_storage").getToken().getUser()')]
        UserInterface $user,
    ): JsonResponse {
        $centrifugoUser = new CentrifugoUser($user);

        $token = $this->credentialsGenerator->generateJwtTokenForUser($centrifugoUser);

        return new JsonResponse(['token' => $token]);
    }
}
