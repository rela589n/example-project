<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Support;

use App\EmployeePortal\Shop\Category\_Support\CategoryFixture;
use App\EmployeePortal\Shop\Product\Features\Create\Port\CreateProductCommand;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Yaml\Yaml;

final class ProductFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        Clock::set(new MockClock(new DateTimeImmutable('2026-01-02T10:00:00+00:00')));

        /** @var array{categories: array<string, string>, products: list<array{id: string, title: string, description: string, price: int, category: string}>} $data */
        $data = Yaml::parseFile(__DIR__ . '/products.yaml');
        $categories = $data['categories'];

        foreach ($data['products'] as $product) {
            $this->commandBus->dispatch(new CreateProductCommand(
                $product['title'],
                $product['description'],
                $product['price'],
                Uuid::fromString($categories[$product['category']]),
                Uuid::fromString($product['id']),
            ));
        }

        Clock::set(new NativeClock());
    }

    public function getDependencies(): array
    {
        return [CategoryFixture::class];
    }
}
