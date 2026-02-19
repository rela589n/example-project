<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\_Features\Index\Port\Cli;

use App\EmployeePortal\Shop\Product\_Features\Index\Port\IndexProductCommand;
use App\EmployeePortal\Shop\Product\ProductCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('app:entity:products:reindex')]
final class ReIndexAllProductsConsoleCommand extends Command
{
    public function __construct(
        private readonly ProductCollection $productCollection,
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Indexes all products into Vespa.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->productCollection->match();

        foreach ($products as $product) {
            $this->commandBus->dispatch(new IndexProductCommand($product->id));
        }

        $output->writeln(sprintf('Indexed %d product(s).', $products->count()));

        return Command::SUCCESS;
    }
}
