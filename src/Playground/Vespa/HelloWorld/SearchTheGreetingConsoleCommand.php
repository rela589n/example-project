<?php

declare(strict_types=1);

namespace App\Playground\Vespa\HelloWorld;

use App\Support\Vespa\VespaClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
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
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of results', '3');
        $this->addOption('grammar', null, InputOption::VALUE_REQUIRED, 'Query grammar type', 'all');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $query */
        $query = $input->getArgument('query');
        /** @var string $limit */
        $limit = $input->getOption('limit');
        /** @var string $grammar */
        $grammar = $input->getOption('grammar');

        $result = $this->vespaClient->search(
            query: $query,
            docType: 'greeting',
            grammar: $grammar,
            limit: (int)$limit,
        );

        $io->success('Search completed');

        $io->writeln(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
