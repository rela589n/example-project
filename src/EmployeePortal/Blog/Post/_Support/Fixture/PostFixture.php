<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\_Support\Fixture;

use App\EmployeePortal\Blog\Post\_Features\Create\Port\CreatePostCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Yaml\Yaml;

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
        /** @var array{posts: list<array{id: string, authorId: string, title: string, description: string}>} $data */
        $data = Yaml::parseFile(__DIR__ . '/posts.yaml');
        $posts = $data['posts'];

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
