<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Anonymous\GetToken\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Authentication\Jwt\Tests\Constraint\ValidJwtTokenPair;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetAnonymousTokenFrontendApiPoint::class)]
final class GetAnonymousTokenFrontendApiPointTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    public function testGetToken(): void
    {
        $response = $this->client->request(
            'GET',
            '/api/example-project/auth/token',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'uk',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        self::assertThat($response->toArray(), new ValidJwtTokenPair());
    }
}
