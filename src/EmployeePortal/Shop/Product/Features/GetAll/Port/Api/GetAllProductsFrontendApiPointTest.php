<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\GetAll\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Entity\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetAllProductsFrontendApiPoint::class)]
final class GetAllProductsFrontendApiPointTest extends ApiTestCase
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

    public function testGetAllProducts(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'GET',
            '/api/example-project/shop/products',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'query' => [
                    'filters' => [
                        'title' => 'vitae quas',
                    ],
                ],
            ],
        );

        dd($response->toArray());

        self::assertSame([], $response->toArray()); // @phpstan-ignore deadCode.unreachable
    }
}
