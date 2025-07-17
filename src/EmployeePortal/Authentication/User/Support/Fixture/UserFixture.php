<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Fixture;

use App\EmployeePortal\Authentication\User\Features\Register\Port\RegisterUserCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final class UserFixture extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function getOrder(): int
    {
        return 10;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'user@test.com',
                'password' => 'jG\Qc_g7;%zE85',
                'id' => '2a977708-1c69-7d38-9074-b388a7f386dc',
            ],
            [
                'email' => 'user2@test.com',
                'password' => 'jG\Qc_g7;%zE85',
                'id' => 'de13a4f3-b43e-74d4-aca9-7ce087a21b73',
            ],
        ];

        foreach ($users as $userData) {
            $command = new RegisterUserCommand(
                $userData['email'],
                $userData['password'],
                $userData['id'],
            );

            $this->commandBus->dispatch($command);
        }
    }
}
