<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Register\Inbox\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Authentication\Jwt\Anonymous\AnonymousUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PhPhD\ApiTesting\Jwt\JwtLoginTrait;

final class RegisterUserFrontendApiPointTest extends ApiTestCase
{
    use JwtLoginTrait;

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

        $response = $this->client->request(
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
//                    'foo' => 'bar',
                ],
            ],
        );

        self::assertSame(
            [],
            $response->toArray(false),
        );

        self::assertResponseStatusCodeSame(200);
    }
}
