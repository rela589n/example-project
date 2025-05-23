<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use Carbon\CarbonImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

#[AsCommand('app:temporal:quote-of-the-day')]
final class QuoteOfTheDayWorkflowConsoleCommand extends Command
{
    private WorkflowClientInterface $workflowClient;

    public function __construct()
    {
        parent::__construct();

        // Run temporal server on your host machine as:
        // temporal server start-dev --ip 0.0.0.0
        $address = 'host.docker.internal:7233';

        $serviceClient = ServiceClient::create($address);

        $this->workflowClient = WorkflowClient::create($serviceClient);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('day', null, InputOption::VALUE_REQUIRED, 'Quote Day', CarbonImmutable::now()->dayOfMonth);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            QuoteOfTheDayWorkflow::class,
            WorkflowOptions::new()
                ->withWorkflowExecutionTimeout(3),
        );

        /** @var string|int $day */
        $day = $input->getOption('day');

        /** @var string $greeting */
        $greeting = $workflow->getQuoteOfTheDay((int)$day);

        $output->writeln(sprintf("Quote of the day:\n<info>%s</info>", $greeting));

        return Command::SUCCESS;
    }
}
