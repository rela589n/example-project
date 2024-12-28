<?php

declare(strict_types=1);

namespace App\Support\CycleBridge\DBAL;

use Cycle\Database\DatabaseManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
        $io = new SymfonyStyle($input, $output);

        $database = $this->databaseManager->database('default');

        if ($database->table('auth_users')->exists()){
            $io->success('Table auth_users exists');
        } else {
            $io->warning('Table auth_users does not exist');
        }

        return Command::SUCCESS;
    }
}
