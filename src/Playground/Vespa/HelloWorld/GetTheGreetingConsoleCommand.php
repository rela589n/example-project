<?php

declare(strict_types=1);

namespace App\Playground\Vespa\HelloWorld;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:vespa:hello:greeting-get', description: 'Get the Greeting from Vespa by ID')]
final class GetTheGreetingConsoleCommand extends Command
{
    public function __construct(
        private readonly VespaClient $vespaClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Document ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $id */
        $id = $input->getArgument('id');

        $io->title('Getting document by ID: ' . $id);

        $result = $this->vespaClient->getDocument(
            namespace: 'default',
            docType: 'greeting',
            id: $id,
        );

        if ($result !== null) {
            $io->success('Document retrieved successfully');

            $io->writeln(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        } else {
            $io->error('Document not found with ID: ' . $id);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
