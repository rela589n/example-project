<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\TransferOwnership\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Blog\Post\Post;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TransferPostOwnershipFrontendApiPoint::class)]
final class TransferPostOwnershipFrontendApiPointTest extends ApiTestCase
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

    public function testTransferPostOwnership(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $newOwnerId = 'de13a4f3-b43e-74d4-aca9-7ce087a21b73';

        $postBefore = $this->entityManager->find(Post::class, 'a2f6d821-6b23-73f4-bb85-6daf4280b72c');
        $initialOwner = $postBefore->owner;

        $this->client->request(
            'POST',
            '/api/example-project/blog/posts/a2f6d821-6b23-73f4-bb85-6daf4280b72c/transfer-ownership',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'newOwnerId' => $newOwnerId,
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $post = $this->entityManager->find(Post::class, 'a2f6d821-6b23-73f4-bb85-6daf4280b72c');
        self::assertNotNull($post, 'Post should exist');

        self::assertSame($initialOwner->id->toRfc4122(), $post->author->id->toRfc4122(), 'Post authorship should remain the same');
        self::assertSame('a2f6d821-6b23-73f4-bb85-6daf4280b72c', $post->id->toRfc4122());
        self::assertSame($newOwnerId, $post->owner->id->toRfc4122());
    }
}
