<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature;

use App\Playground\Temporal\Signature\Workflow\SignatureWorkflow;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Exception\Client\WorkflowFailedException;
use Temporal\Exception\Failure\ApplicationFailure;

#[AsCommand('app:temporal:signature')]
final class SignatureWorkflowConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.client')]
        private readonly WorkflowClientInterface $workflowClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'documentId',
            InputArgument::REQUIRED,
            'Document ID to sign',
        );
        $this->addArgument(
            'password',
            InputArgument::REQUIRED,
            'Password for signing',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $documentId */
        $documentId = $input->getArgument('documentId');

        /** @var string $password */
        $password = $input->getArgument('password');

        $workflow = SignatureWorkflow::create($this->workflowClient);

        try {
            $workflow->run($documentId, $password);
        } catch (WorkflowFailedException $e) {
            $applicationFailure = $e->getPrevious();

            while (!$applicationFailure instanceof ApplicationFailure) {
                if (null === $applicationFailure) {
                    throw $e;
                }

                $applicationFailure = $applicationFailure->getPrevious();
            }

            if ('validation' !== $applicationFailure->getType()) {
                throw $e;
            }

            /** @var non-empty-list<ConstraintViolationInterface> $constraintViolations */
            $constraintViolations = $applicationFailure->getDetails()->getValues();

            $io->error('Validation Failed:');
            $io->table(
                ['Property Path', 'Message', 'Parameters'],
                $this->getViolationRows($constraintViolations),
            );

            return Command::FAILURE;
        }

        $io->success('Signature workflow completed for document: '.$documentId);

        return Command::SUCCESS;
    }


    /**
     * Convert constraint violations to a table row format.
     *
     * @param non-empty-list<ConstraintViolationInterface> $constraintViolations
     *
     * @return non-empty-list<list<mixed>>
     */
    private function getViolationRows(array $constraintViolations): array
    {
        $rows = [];
        foreach ($constraintViolations as $violation) {
            $rows[] = [
                $violation->getPropertyPath(),
                $violation->getMessage(),
                json_encode($violation->getParameters(), JSON_THROW_ON_ERROR),
            ];
        }

        return $rows;
    }
}
