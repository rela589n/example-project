<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Product\Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Entity\Product\Product;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CreateProductFrontendApiPoint::class)]
final class CreateProductFrontendApiPointTest extends ApiTestCase
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

    public function testCreateProduct(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'POST',
            '/api/example-project/entity/products',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        $responseArray = $response->toArray();
        self::assertArrayHasKey('id', $responseArray);

        $id = Uuid::fromString((string) $responseArray['id']);

        $entity = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        self::assertNotNull($entity, 'Product should be created');
        self::assertLessThanOrEqual(10, CarbonImmutable::now()->diffInSeconds($entity->createdAt));
    }
}
