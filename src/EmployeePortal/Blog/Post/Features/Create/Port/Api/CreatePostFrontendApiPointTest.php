<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Blog\Post\Post;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CreatePostFrontendApiPoint::class)]
final class CreatePostFrontendApiPointTest extends ApiTestCase
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

    public function testCreatePost(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $postId = Uuid::v7()->toRfc4122();

        $this->client->request(
            'POST',
            '/api/example-project/blog/posts',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'id' => $postId,
                    'title' => 'Test Post Title',
                    'description' => 'This is a test post description.',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        $post = $this->entityManager->find(Post::class, $postId);

        self::assertNotNull($post, 'Post should be created');
        self::assertSame($postId, $post->id->toRfc4122());
        self::assertSame('Test Post Title', $post->title);
        self::assertSame('This is a test post description.', $post->description);
    }
}
