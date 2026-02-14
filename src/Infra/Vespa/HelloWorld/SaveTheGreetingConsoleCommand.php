<?php

declare(strict_types=1);

namespace App\Infra\Vespa\HelloWorld;

use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:vespa:hello:greeting-save', description: 'Store the Greeting to Vespa')]
final class SaveTheGreetingConsoleCommand extends Command
{
    public function __construct(
        private readonly VespaClient $vespaClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::OPTIONAL, 'Document ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ?string $id */
        $id = $input->getArgument('id');
        $id ??= Uuid::v7()->toRfc4122();

        $faker = Factory::create();
        $faker->seed(random_int(1, 123));

        $name = $faker->name();
        $arbitraryText = $faker->realText(random_int(100, 200));

        $fields = [
            'name' => $name,
            'message' =>  'Hello, ' . $name . '! '. $arbitraryText,
        ];

        $io->title('Saving document');
        $io->writeln(json_encode($fields, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $result = $this->vespaClient->feedDocument(
            namespace: 'default',
            docType: 'greeting',
            id: $id,
            fields: $fields,
        );

        $io->success('Document saved successfully');

        $io->writeln(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return Command::SUCCESS;
    }
}
