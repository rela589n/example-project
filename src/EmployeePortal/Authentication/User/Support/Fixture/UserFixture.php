<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Support\Fixture;

use App\EmployeePortal\Authentication\User\Features\Register\Port\RegisterUserCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

final  class UserFixture extends Fixture
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $command = new RegisterUserCommand(
            '2a977708-1c69-7d38-9074-b388a7f386dc',
            'user@test.com',
            'jG\Qc_g7;%zE85',
        );

        $this->commandBus->dispatch($command);
    }
}
