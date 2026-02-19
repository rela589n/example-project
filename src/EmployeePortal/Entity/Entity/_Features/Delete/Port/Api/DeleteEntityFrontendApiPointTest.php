<?php

declare(strict_types=1);

namespace App\EmployeePortal\Entity\Entity\_Features\Delete\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Entity\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeleteEntityFrontendApiPoint::class)]
final class DeleteEntityFrontendApiPointTest extends ApiTestCase
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

    public function testDeleteEntity(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $this->client->request(
            'DELETE',
            '/api/example-project/entity/entities/3a24fc63-756b-7d28-b6df-a2edb0990e4b',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(204);

        $this->entityManager->clear();

        $entity = $this->entityManager->getRepository(Entity::class)->findOneBy(['id' => '3a24fc63-756b-7d28-b6df-a2edb0990e4b']);

        self::assertNull($entity, 'Entity should be deleted');
    }
}
