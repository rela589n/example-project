<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Register\Inbox\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use PhPhD\ApiTesting\Jwt\JwtLoginTrait;

final class RegisterUserFrontendApiPointTest extends ApiTestCase
{
    use JwtLoginTrait;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    public function testRegister(): void
    {
        $token = $this->login('anonymous');

        $response = $this->client->request(
            'GET',
            '/api/example-project/auth/register',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
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
