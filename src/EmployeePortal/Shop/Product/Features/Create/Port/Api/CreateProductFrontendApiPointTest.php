<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Shop\Product\Product;
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
            '/api/example-project/shop/products',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'title' => 'Holy Bible',
                    'categoryId' => '3a24fc63-756c-7d28-b6df-a2edb0990e02',
                    'priceUnitAmount' => 1200,
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        /** @var array{id: string} $responseArray */
        $responseArray = $response->toArray();

        $id = Uuid::fromString($responseArray['id']);

        $entity = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        self::assertNotNull($entity, 'Product should be created');

        self::assertSame('Holy Bible', $entity->title->title);
        self::assertSame('3a24fc63-756c-7d28-b6df-a2edb0990e02', $entity->category->id->toRfc4122());
        self::assertSame(1200, $entity->price->unitAmount);

        self::assertLessThanOrEqual(10, CarbonImmutable::now()->diffInSeconds($entity->createdAt));
    }
}
