<?php

declare(strict_types=1);

namespace App\EmployeePortal\Blog\Post\Comment\Features\Add\Port;

use App\EmployeePortal\Blog\Post\PostCollection;
use App\EmployeePortal\Blog\User\UserCollection;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class AddPostCommentService
{
    public function __construct(
        public ClockInterface $clock,
        public EntityManagerInterface $entityManager,
        public PostCollection $postCollection,
        public UserCollection $userCollection,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(AddPostCommentCommand $command): void
    {
        $command->process($this);
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::instance($this->clock->now());
    }
}
