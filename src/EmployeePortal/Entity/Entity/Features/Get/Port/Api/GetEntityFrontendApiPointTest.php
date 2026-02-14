<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\Features\Get\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetEntityFrontendApiPoint::class)]
final class GetEntityFrontendApiPointTest extends ApiTestCase
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

    public function testGetEntity(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'GET',
            "/api/example-project/entity/entities/3a24fc63-756b-7d28-b6df-a2edb0990e4b",
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $responseData = $response->toArray();

        self::assertSame([
            'id' => '3a24fc63-756b-7d28-b6df-a2edb0990e4b',
            'created_at' => '2026-01-01T12:00:00+00:00',
            'updated_at' => '2026-01-01T12:00:00+00:00',
        ], $responseData);
    }
}
