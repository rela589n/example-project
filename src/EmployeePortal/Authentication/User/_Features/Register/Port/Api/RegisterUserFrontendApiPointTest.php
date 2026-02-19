<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\_Features\Register\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Authentication\Jwt\Anonymous\AnonymousUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegisterUserFrontendApiPoint::class)]
final class RegisterUserFrontendApiPointTest extends ApiTestCase
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

    public function testRegister(): void
    {
        $token = $this->jwtManager->create(new AnonymousUser());

        $this->client->request(
            'POST',
            '/api/example-project/auth/register',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'email' => 'example@example.com',
                    'password' => 'jG\Qc_g7;%zE85',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);
    }
}
