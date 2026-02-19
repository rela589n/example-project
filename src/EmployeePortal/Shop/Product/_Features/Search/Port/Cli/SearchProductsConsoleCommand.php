<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Search\Port\Cli;

use App\EmployeePortal\Shop\Product\_Features\Search\Port\SearchProductsQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:entity:products:search',
    description: 'Search products in the Vespa index',
)]
final class SearchProductsConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@query.bus')]
        private readonly MessageBusInterface $queryBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::OPTIONAL, 'Search query', '')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of results to return', '10')
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Number of results to skip', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $query */
        $query = $input->getArgument('query');

        /** @var numeric-string $limit */
        $limit = $input->getOption('limit');

        /** @var numeric-string $offset */
        $offset = $input->getOption('offset');

        $searchQuery = new SearchProductsQuery($query, (int)$offset, (int)$limit);

        $this->queryBus->dispatch($searchQuery);

        $results = $searchQuery->getResults();

        $output->writeln((string)json_encode($results, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return Command::SUCCESS;
    }
}
