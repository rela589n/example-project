<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Features\GetMy\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetMyPostsFrontendApiPoint::class)]
final class GetMyPostsFrontendApiPointTest extends ApiTestCase
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

    public function testGetMyPostsList(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'GET',
            '/api/example-project/blog/posts/my',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $responseData = $response->toArray();

        self::assertSame(
            [
                [
                    'id' => 'a2f6d821-6b23-73f4-bb85-6daf4280b72c',
                    'title' => 'First Sample Post',
                    'description' => 'This is the first sample post created by the fixture.',
                ],
                [
                    'id' => 'e013d514-a0a0-7813-b7df-679e40907dba',
                    'title' => 'Second Sample Post',
                    'description' => 'This is the second sample post created by the fixture.',
                ],
            ],
            $responseData,
        );
    }
}
