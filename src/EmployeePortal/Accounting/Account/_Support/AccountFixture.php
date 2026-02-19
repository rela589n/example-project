<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\_Support;

use App\EmployeePortal\Accounting\Account\_Features\Create\Port\CreateAccountCommand;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final class AccountFixture extends Fixture
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        Clock::set(new MockClock(new DateTimeImmutable('2023-01-01T12:00:00+00:00')));

        $accountsByUser = [
            '2a977708-1c69-7d38-9074-b388a7f386dc' => [
                ['id' => 'a3a8c8c9-2336-753b-b970-51d2540a40ec'],
                ['id' => '1bb783a8-0813-7a43-801a-5c0e90ad9841'],
            ],
            'de13a4f3-b43e-74d4-aca9-7ce087a21b73' => [
                ['id' => '268cef29-798b-7512-9560-a6dec7af72fd'],
            ],
        ];

        foreach ($accountsByUser as $userId => $accounts) {
            foreach ($accounts as $account) {
                $command = new CreateAccountCommand(
                    $account['id'],
                    $userId,
                );

                $this->commandBus->dispatch($command);
            }
        }

        Clock::set(new NativeClock());
    }
}
