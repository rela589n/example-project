<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\Features\Register\Port\Cli;

use App\EmployeePortal\Authentication\User\Features\Register\Port\RegisterUserCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand('app:auth:user:register')]
final class RegisterUserConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Registers a new user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $password */
        $password = $input->getArgument('password');

        $registerUserCommand = new RegisterUserCommand(
            $email,
            $password,
            Uuid::v7()->toRfc4122(),
        );

        $this->commandBus->dispatch($registerUserCommand);

        $output->writeln('User successfully registered.');

        return Command::SUCCESS;
    }
}
