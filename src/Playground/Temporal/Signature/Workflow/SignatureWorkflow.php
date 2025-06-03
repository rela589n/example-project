<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow;

use App\Playground\Temporal\Signature\Workflow\Ack\AcknowledgeActivity;
use App\Playground\Temporal\Signature\Workflow\Ack\AcknowledgeSignatureCommand;
use App\Playground\Temporal\Signature\Workflow\Ack\SignFailFlag;
use App\Playground\Temporal\Signature\Workflow\Sign\SignDocumentActivity;
use App\Playground\Temporal\Signature\Workflow\Sign\SignDocumentCommand;
use Carbon\CarbonInterval;
use Exception;
use Generator;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Temporal\Workflow\Saga;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[WorkflowInterface]
#[AssignWorker('default')]
final readonly class SignatureWorkflow
{
    private Saga $saga;

    private SignDocumentActivity|Proxy $signActivity;

    private AcknowledgeActivity|Proxy $acknowledgeActivity;

    public function __construct()
    {
        $this->saga = new Saga()->setParallelCompensation(true);
        $this->signActivity = SignDocumentActivity::create();
        $this->acknowledgeActivity = AcknowledgeActivity::create();
    }

    public static function create(WorkflowClientInterface $workflowClient): self|Proxy
    {
        return $workflowClient->newWorkflowStub(
            self::class,
            WorkflowOptions::new(),
        );
    }

    #[WorkflowMethod]
    public function run(string $documentId, string $password): Generator
    {
        try {
            yield Workflow::timer(CarbonInterval::seconds(1));

            $signCommand = new SignDocumentCommand($documentId, $password);

            $this->saga->addCompensation(fn () => $this->signActivity->cancel($signCommand));

            /** @var string $signedFilePath */
            // $signedFilePath = yield $this->signActivity->sign($signCommand->documentId, $signCommand->password);
            $signedFilePath = yield $this->signActivity->sign($signCommand);

            yield Workflow::timer(CarbonInterval::seconds(1));

            $acknowledgeCommand = new AcknowledgeSignatureCommand($documentId, $signedFilePath, SignFailFlag::NONE);

            $this->saga->addCompensation(fn () => $this->acknowledgeActivity->cancel($acknowledgeCommand));

            yield $this->acknowledgeActivity->acknowledge($acknowledgeCommand);
        } catch (Exception $e) {
            yield $this->saga->compensate();

            throw $e;
        }
    }
}

