<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\User\Features\SendEvent\Port\Cli;

use App\Infra\WebSocket\User\Features\SendEvent\Port\SendEventCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

use function random_int;

#[AsCommand(
    name: 'app:web-socket:send-test-event',
    description: 'Send test centrifugo event to given user',
)]
final class SendTestEventConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@ws.event.bus')]
        private readonly MessageBusInterface $wsEventBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->addArgument('user-id', InputArgument::REQUIRED, 'The ID of the user to send the notification to.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $uuid */
        $uuid = $input->getArgument('user-id');
        $userId = Uuid::fromString($uuid);

        $this->wsEventBus->dispatch(new SendEventCommand($userId, 'test_event', ['hello' => 'world'.random_int(0, 100)]));

        $io->success('Test event has been sent successfully!');

        return Command::SUCCESS;
    }
}
