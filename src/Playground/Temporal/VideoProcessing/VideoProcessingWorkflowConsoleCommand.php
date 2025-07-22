<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VideoProcessing;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

use function array_map;
use function explode;
use function ltrim;
use function rtrim;

#[AsCommand('app:temporal:video-processing')]
final class VideoProcessingWorkflowConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('length', InputArgument::REQUIRED, 'Video length');
        $this->addOption('fail', null, InputOption::VALUE_NONE, 'Simulate a failure in the workflow');
        $this->addOption('beat-range', null, InputOption::VALUE_REQUIRED, 'Beat range in seconds: [min,max]', '[1,1]');
        $this->addOption('beat-limit', null, InputOption::VALUE_REQUIRED, 'Heartbeat only until this iteration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $length = $this->getLength($input);

        $beatRange = $this->getBeatRange($input);

        $beatLimit = $this->getHeartbeatLimit($input);

        /** @var bool $fail */
        $fail = $input->getOption('fail');

        $workflow = $this->workflowClient
            ->withTimeout($length * 1.5)
            ->newWorkflowStub(
                VideoProcessingWorkflow::class,
                WorkflowOptions::new(),
            )
        ;

        /** @var string $result */
        $result = $workflow->process($length, $beatRange, $beatLimit, $fail);

        $io->success($result);

        return Command::SUCCESS;
    }

    /** @return array{int,int} */
    private function getBeatRange(InputInterface $input): array
    {
        /** @var string $beatInterval */
        $beatInterval = $input->getOption('beat-range');

        $beatInterval = rtrim(ltrim($beatInterval, '['), ']');

        /** @var non-empty-list<int> $beatRange */
        $beatRange = array_map(intval(...), explode(',', $beatInterval));

        if (!isset($beatRange[1])) {
            $beatRange[1] = $beatRange[0];
        }

        return $beatRange;
    }

    private function getLength(InputInterface $input): int
    {
        /** @var string $length */
        $length = $input->getArgument('length');

        return (int)$length;
    }

    private function getHeartbeatLimit(InputInterface $input): ?int
    {
        /** @var ?string $beatLimit */
        $beatLimit = $input->getOption('beat-limit');

        if (null === $beatLimit) {
            return null;
        }

        return (int)$beatLimit;
    }
}
