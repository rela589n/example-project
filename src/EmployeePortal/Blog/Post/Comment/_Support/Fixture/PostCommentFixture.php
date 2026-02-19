<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\_Support\Fixture;

use App\EmployeePortal\Blog\Post\_Support\Fixture\PostFixture;
use App\EmployeePortal\Blog\Post\Comment\Features\Add\Port\AddPostCommentCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final class PostCommentFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function getDependencies(): array
    {
        return [PostFixture::class];
    }

    public function load(ObjectManager $manager): void
    {
        $comments = [
            [
                'id' => '31fb1073-7cad-782d-8579-d78398556dd5',
                'userId' => '2a977708-1c69-7d38-9074-b388a7f386dc', // user@test.com
                'postId' => 'a2f6d821-6b23-73f4-bb85-6daf4280b72c', // First Sample Post
                'text' => 'This is a comment on the first post.',
            ],
            [
                'id' => 'a2110234-1af5-7e08-b935-84e0f83c1ca6',
                'userId' => 'de13a4f3-b43e-74d4-aca9-7ce087a21b73', // user2@test.com
                'postId' => 'a2f6d821-6b23-73f4-bb85-6daf4280b72c', // First Sample Post
                'text' => 'Another comment on the first post by a different user.',
            ],
            [
                'id' => '47d0dd49-d79d-7894-ad78-bf3a88275805',
                'userId' => '2a977708-1c69-7d38-9074-b388a7f386dc', // user@test.com
                'postId' => 'e013d514-a0a0-7813-b7df-679e40907dba', // Second Sample Post
                'text' => 'This is a comment on the second post.',
            ],
            [
                'id' => '86c0067a-07f3-72f7-ba6f-832a434a5ab4',
                'userId' => '2a977708-1c69-7d38-9074-b388a7f386dc', // user@test.com
                'postId' => '3d6c586a-eba5-7085-9121-f4888e9fd80f', // Third Sample Post
                'text' => 'This is a comment on the third post.',
            ],
        ];

        foreach ($comments as $commentData) {
            $command = new AddPostCommentCommand(
                $commentData['userId'],
                $commentData['postId'],
                $commentData['text'],
                $commentData['id'],
            );

            $this->commandBus->dispatch($command);
        }
    }
}
