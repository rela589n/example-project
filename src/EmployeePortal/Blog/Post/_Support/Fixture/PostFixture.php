<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Support\Fixture;

use App\EmployeePortal\Blog\Post\Features\Create\Port\CreatePostCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final class PostFixture extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function getOrder(): int
    {
        return 20;
    }

    public function load(ObjectManager $manager): void
    {
        $posts = [
            [
                'id' => 'a2f6d821-6b23-73f4-bb85-6daf4280b72c',
                'authorId' => '2a977708-1c69-7d38-9074-b388a7f386dc', // user@test.com
                'title' => 'First Sample Post',
                'description' => 'This is the first sample post created by the fixture.',
            ],
            [
                'id' => 'e013d514-a0a0-7813-b7df-679e40907dba',
                'authorId' => '2a977708-1c69-7d38-9074-b388a7f386dc', // user@test.com
                'title' => 'Second Sample Post',
                'description' => 'This is the second sample post created by the fixture.',
            ],
            [
                'id' => '3d6c586a-eba5-7085-9121-f4888e9fd80f',
                'authorId' => 'de13a4f3-b43e-74d4-aca9-7ce087a21b73', // user2@test.com
                'title' => 'Third Sample Post',
                'description' => 'This is the third sample post created by the fixture, by a different user.',
            ],
        ];

        foreach ($posts as $postData) {
            $command = new CreatePostCommand(
                $postData['id'],
                $postData['authorId'],
                $postData['title'],
                $postData['description'],
            );

            $this->commandBus->dispatch($command);
        }
    }
}
