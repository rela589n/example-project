<?php

declare(strict_types=1);

namespace App\Playground\Vespa\HelloWorld;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:vespa:hello:greeting-search', description: 'Search for Greetings in Vespa')]
final class SearchTheGreetingConsoleCommand extends Command
{
    public function __construct(
        private readonly VespaClient $vespaClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('query', InputArgument::OPTIONAL, 'Search query', '*');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $query */
        $query = $input->getArgument('query');

        $result = $this->vespaClient->search(
            query: $query,
            docType: 'greeting',
        );

        $io->success('Search completed');

        $io->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
