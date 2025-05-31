<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Pairwise;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:pairwise')]
final class PairwiseWorkflowConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $workflow = $this->workflowClient
            ->newWorkflowStub(
                PairwiseWorkflow::class,
                WorkflowOptions::new(),
            );

        $result = $workflow->pair();

        $io->writeln($result);

        return Command::SUCCESS;
    }
}

