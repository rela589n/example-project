<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\_Features\GetList\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetAccountsListFrontendApiPoint::class)]
final class GetAccountsListFrontendApiPointTest extends ApiTestCase
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

    public function testGetAccountsList(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'GET',
            '/api/example-project/accounting/accounts',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        self::assertSame([
            [
                'id' => 'a3a8c8c9-2336-753b-b970-51d2540a40ec',
                'number' => 1,
                'createdAt' => '2023-01-01T12:00:00+00:00',
            ],
            [
                'id' => '1bb783a8-0813-7a43-801a-5c0e90ad9841',
                'number' => 2,
                'createdAt' => '2023-01-01T12:00:00+00:00',
            ],
        ], $response->toArray());
    }
}
