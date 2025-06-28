<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Refresh;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Authentication\Jwt\Tests\Constraint\ValidJwtTokenPair;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class RefreshJwtTokenApiPointTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    public function testRefreshToken(): void
    {
        $anonymousTokenPair = $this->getAnonymousTokenPair();

        ['refreshToken' => $refreshToken] = $anonymousTokenPair;

        $refreshedTokenResponse = $this->client->request(
            'POST',
            '/api/example-project/auth/token/refresh',
            [
                'json' => [
                    'refreshToken' => $refreshToken,
                ],
            ],
        );

        $refreshedTokenPair = $refreshedTokenResponse->toArray();

        self::assertThat($refreshedTokenPair, new ValidJwtTokenPair());

        self::assertNotSame($refreshedTokenPair, $anonymousTokenPair);
    }

    /** @return array{token: string, refreshToken: string} */
    private function getAnonymousTokenPair(): array
    {
        $anonymousTokenResponse = $this->client->request('GET', '/api/example-project/auth/token');

        /** @var array{token: string, refreshToken: string} $anonymousTokenPair */
        $anonymousTokenPair = $anonymousTokenResponse->toArray();

        return $anonymousTokenPair;
    }
}
