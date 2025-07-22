<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Add\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Blog\Post\Comment\PostComment;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddPostCommentFrontendApiPoint::class)]
final class AddPostCommentFrontendApiPointTest extends ApiTestCase
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

    public function testAddComment(): void
    {
        $user = new JWTUser('2a977708-1c69-7d38-9074-b388a7f386dc', ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $this->client->request(
            'POST',
            '/api/example-project/blog/posts/a2f6d821-6b23-73f4-bb85-6daf4280b72c/comments',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'id' => '4a237eec-f771-7bf8-b597-13d20efcdd1e',
                    'text' => 'This is a test comment.',
                ],
            ],
        );

        // Assert response status code is 201 Created
        self::assertResponseStatusCodeSame(201);

        $comment = $this->entityManager->find(PostComment::class, '4a237eec-f771-7bf8-b597-13d20efcdd1e');

        self::assertNotNull($comment, 'Comment should be created');
        self::assertSame('4a237eec-f771-7bf8-b597-13d20efcdd1e', $comment->id->toRfc4122());
        self::assertSame('2a977708-1c69-7d38-9074-b388a7f386dc', $comment->author->id->toRfc4122());
        self::assertSame('This is a test comment.', $comment->text);
    }
}
