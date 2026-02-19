<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\_Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Accounting\Account\Account;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateAccountFrontendApiPoint::class)]
final class CreateAccountFrontendApiPointTest extends ApiTestCase
{
    private Client $client;

    private JWTTokenManagerInterface $jwtManager;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');
        $this->jwtManager = $jwtManager;

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
    }

    public function testCreateAccount(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $this->client->request(
            'POST',
            '/api/example-project/accounting/account',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'id' => 'd00fbee7-d5e8-7e68-b1ff-e794250e7cfa',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        $account = $this->entityManager->getRepository(Account::class)->findOneBy(['id.id' => 'd00fbee7-d5e8-7e68-b1ff-e794250e7cfa']);

        self::assertNotNull($account, 'Account should be created');
        self::assertSame('d00fbee7-d5e8-7e68-b1ff-e794250e7cfa', $account->getId()->toRfc4122());
        self::assertSame('2a977708-1c69-7d38-9074-b388a7f386dc', $account->getUserId()->toRfc4122());
        self::assertGreaterThan(0, $account->getNumber());
        self::assertLessThanOrEqual(10, CarbonImmutable::now()->diffInSeconds($account->getCreatedAt()));
    }
}
