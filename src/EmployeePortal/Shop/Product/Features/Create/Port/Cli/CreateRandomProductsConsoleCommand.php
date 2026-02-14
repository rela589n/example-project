<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\Product\Features\Create\Port\Cli;

use App\EmployeePortal\Shop\Category\Category;
use App\EmployeePortal\Shop\Category\CategoryCollection;
use App\EmployeePortal\Shop\Category\Features\Create\Port\CreateCategoryCommand;
use App\EmployeePortal\Shop\Product\Features\Create\Port\CreateProductCommand;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('app:entity:products:create-random')]
final class CreateRandomProductsConsoleCommand extends Command
{
    private const int PRODUCTS_COUNT = 10;
    private const int CATEGORIES_COUNT = 3;

    public function __construct(
        #[Autowire('@command.bus')]
        private readonly MessageBusInterface $commandBus,
        private readonly CategoryCollection $categoryCollection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::CATEGORIES_COUNT; $i++) {
            /** @var string $categoryName */
            $categoryName = $faker->words(2, true);

            $this->commandBus->dispatch(new CreateCategoryCommand($categoryName));
        }

        $items = $this->categoryCollection->match();

        for ($i = 0; $i < self::PRODUCTS_COUNT; $i++) {
            /** @var Category $category */
            $category = $items->get(random_int(0, $items->count() - 1)); // @phpstan-ignore argument.type

            /** @var string $productTitle */
            $productTitle = $faker->words(2, true);
            $command = new CreateProductCommand(
                $productTitle,
                $faker->numberBetween(0, 10_000_00),
                $category->id,
            );

            $this->commandBus->dispatch($command);
        }

        $output->writeln('Products created');

        return Command::SUCCESS;
    }
}
