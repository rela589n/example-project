<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Features\Update\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Shop\Category\Category;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateCategoryFrontendApiPoint::class)]
final class UpdateCategoryFrontendApiPointTest extends ApiTestCase
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

    public function testUpdateCategory(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $this->client->request(
            'PUT',
            '/api/example-project/shop/categories/3a24fc63-756c-7d28-b6df-a2edb0990e01',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => ['name' => 'Updated Category Name'],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $this->entityManager->clear();

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => '3a24fc63-756c-7d28-b6df-a2edb0990e01']);
        self::assertNotNull($category, 'Category should exist');
        self::assertSame('Updated Category Name', $category->name);
    }
}
