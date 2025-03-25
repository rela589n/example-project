<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Actions\Login\Inbox\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Authentication\Jwt\Anonymous\AnonymousUser;
use App\EmployeePortal\Authentication\Jwt\Tests\Constraint\ValidJwtTokenPair;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PhPhD\ApiTesting\Jwt\JwtLoginTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LoginUserFrontendApiPoint::class)]
final class LoginUserFrontendApiPointTest extends ApiTestCase
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

    public function testLogin(): void
    {
        $token = $this->jwtManager->create(new AnonymousUser());

        $response = $this->client->request(
            'POST',
            '/api/example-project/auth/login',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'email' => 'user@test.com',
                    'password' => 'jG\Qc_g7;%zE85',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);
        self::assertThat($response->toArray(), new ValidJwtTokenPair());
    }
}
