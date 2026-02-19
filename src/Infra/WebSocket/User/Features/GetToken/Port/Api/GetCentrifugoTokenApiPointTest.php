<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\User\Features\GetToken\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/** @internal */
#[CoversClass(GetCentrifugoTokenFrontendApiPoint::class)]
final class GetCentrifugoTokenApiPointTest extends ApiTestCase
{
    private Client $client;

    private JWTTokenManagerInterface $jwtManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');
        $this->jwtManager = $jwtManager;
    }

    public function testReturnsNewCentrifugoToken(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'GET',
            '/api/example-project/infra/web-socket/centrifugo/token',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'uk',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $responseArray = $response->toArray(false);

        self::assertArrayHasKey('token', $responseArray);
        self::assertIsString($responseArray['token']);
    }
}
