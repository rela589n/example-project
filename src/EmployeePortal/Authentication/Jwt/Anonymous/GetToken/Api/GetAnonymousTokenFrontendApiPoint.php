<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Anonymous\GetToken\Api;

use App\EmployeePortal\Authentication\Jwt\Anonymous\AnonymousUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use OpenApi\Attributes as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[ApiDoc\Get(
    summary: 'Anonymous Token',
    security: [],
    tags: ['Token'],
    parameters: [
        new ApiDoc\Parameter(ref: '#/components/parameters/HeaderAcceptLanguage'),
        new ApiDoc\Parameter(ref: '#/components/parameters/HeaderAccept'),
        new ApiDoc\Parameter(ref: '#/components/parameters/HeaderContentType'),
    ],
    responses: [
        new ApiDoc\Response(
            response: 200,
            description: 'Anonymous token',
            content: new ApiDoc\MediaType(
                mediaType: 'application/json',
                schema: new ApiDoc\Schema(ref: '#/components/schemas/JwtTokenPair'),
            ),
        ),
    ],
)]
#[AsController]
final readonly class GetAnonymousTokenFrontendApiPoint
{
    public function __construct(
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
    ) {
    }

    #[Route(
        path: '/token',
        name: 'api_example_project_auth_anonymous_token',
        methods: [Request::METHOD_GET],
    )]
    public function __invoke(TokenStorageInterface $tokenStorage): Response
    {
        if (null !== $tokenStorage->getToken()) {
            return new JsonResponse([
                'error' => 'authorized_user',
                'errorDescription' => '',
            ], Response::HTTP_FORBIDDEN);
        }

        return $this->authenticationSuccessHandler->handleAuthenticationSuccess(new AnonymousUser());
    }
}
