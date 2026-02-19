<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Shop\Category\Category;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CreateCategoryFrontendApiPoint::class)]
final class CreateCategoryFrontendApiPointTest extends ApiTestCase
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

    public function testCreateCategory(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $response = $this->client->request(
            'POST',
            '/api/example-project/shop/categories',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => ['name' => 'Test Category'],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        /** @var array{id: string} $responseArray */
        $responseArray = $response->toArray();

        $id = Uuid::fromString($responseArray['id']);

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);

        self::assertNotNull($category, 'Category should be created');
        self::assertSame('Test Category', $category->name);
        self::assertLessThanOrEqual(10, CarbonImmutable::now()->diffInSeconds($category->createdAt));
    }
}
