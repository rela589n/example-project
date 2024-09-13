<?php

declare(strict_types=1);

namespace App\Support\CycleBridgeBundle\DBAL;

use Cycle\Database\DatabaseManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand('app:cycle:check-connection')]
final class CheckConnectionConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@app_cycle_bridge.dbal')]
        private readonly DatabaseManager $databaseManager,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $database = $this->databaseManager->database('default');

        dd($database->table('users')->exists());

        return Command::SUCCESS;
    }
}
