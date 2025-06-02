<?php

declare(strict_types=1);

namespace App\Playground\Temporal\IntertwinedSequence;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:intertwined-sequence')]
final class IntertwinedSequenceWorkflowConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'The sequence limit', '5');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $limit */
        $limit = $input->getOption('limit');
        $limit = (int)$limit;

        $workflow = $this->workflowClient
            ->newWorkflowStub(
                IntertwinedSequenceWorkflow::class,
                WorkflowOptions::new(),
            );

        $results = $workflow->execute($limit);

        $io->success('Results: ' .json_encode($results, JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
