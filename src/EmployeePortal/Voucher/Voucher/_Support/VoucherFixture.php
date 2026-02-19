<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\_Support;

use App\EmployeePortal\Voucher\Voucher\_Features\Create\Port\CreateVoucherCommand;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final class VoucherFixture extends Fixture
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        Clock::set(new MockClock(new DateTimeImmutable('2023-01-01T12:00:00+00:00')));

        $this->commandBus->dispatch(new CreateVoucherCommand(100, Uuid::fromBase58('4KKKvRooJfif3jaGCbEMuE')));

        Clock::set(new NativeClock());
    }
}
