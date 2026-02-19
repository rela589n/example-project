<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Category\_Support;

use App\EmployeePortal\Shop\Category\Features\Create\Port\CreateCategoryCommand;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class CategoryFixture extends Fixture
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        Clock::set(new MockClock(new DateTimeImmutable('2026-01-01T12:00:00+00:00')));

        $this->commandBus->dispatch(new CreateCategoryCommand('Electronics', Uuid::fromString('3a24fc63-756c-7d28-b6df-a2edb0990e01')));
        $this->commandBus->dispatch(new CreateCategoryCommand('Books', Uuid::fromString('3a24fc63-756c-7d28-b6df-a2edb0990e02')));
        $this->commandBus->dispatch(new CreateCategoryCommand('Clothing', Uuid::fromString('3a24fc63-756c-7d28-b6df-a2edb0990e03')));

        Clock::set(new NativeClock());
    }
}
