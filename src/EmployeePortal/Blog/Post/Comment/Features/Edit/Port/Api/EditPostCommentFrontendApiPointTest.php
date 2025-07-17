<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Edit\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Blog\Post\Comment\PostComment;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditPostCommentFrontendApiPoint::class)]
final class EditPostCommentFrontendApiPointTest extends ApiTestCase
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

    public function testEditComment(): void
    {
        $userId = '2a977708-1c69-7d38-9074-b388a7f386dc';
        $user = new JWTUser($userId, ['ROLE_USER']);
        $token = $this->jwtManager->create($user);

        $this->client->request(
            'PUT',
            '/api/example-project/blog/posts/a2f6d821-6b23-73f4-bb85-6daf4280b72c/comments/31fb1073-7cad-782d-8579-d78398556dd5',
            [
                'auth_bearer' => $token,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'text' => 'This is an updated test comment.',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $comment = $this->entityManager->find(PostComment::class, '31fb1073-7cad-782d-8579-d78398556dd5');

        self::assertNotNull($comment, 'Comment should exist');
        self::assertSame('31fb1073-7cad-782d-8579-d78398556dd5', $comment->id->toRfc4122());
        self::assertSame('2a977708-1c69-7d38-9074-b388a7f386dc', $comment->author->id->toRfc4122());
        self::assertSame('This is an updated test comment.', $comment->text);
    }
}
