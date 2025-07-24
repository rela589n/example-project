<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign;

use Exception;
use LogicException;
use Monolog\Attribute\WithMonologChannel;
use PhPhD\ExceptionalValidation\Mapper\ExceptionMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Temporal\Activity;
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Activity\ActivityOptions;
use Temporal\DataConverter\EncodedValues;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Internal\Workflow\Proxy;
use Temporal\Workflow;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;
use Webmozart\Assert\Assert;

use function iterator_to_array;

#[ActivityInterface('SignActivity.')]
#[AssignWorker('default')]
#[WithMonologChannel('signature')]
final readonly class SignDocumentActivity
{
    public function __construct(
        private LoggerInterface $logger,
        /** @var ExceptionMapper<ConstraintViolationListInterface> */
        #[Autowire(service: ExceptionMapper::class.'<'.ConstraintViolationListInterface::class.'>')]
        private ExceptionMapper $exceptionMapper,
    ) {
    }

    public static function create(): self|Proxy
    {
        return Workflow::newActivityStub(
            self::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(3),
        );
    }

    #[ActivityMethod]
    public function sign(SignDocumentCommand|string $commandOrDocumentId, ?string $password = null): string
    {
        if (null !== $password) {
            Assert::string($commandOrDocumentId);

            $command = new SignDocumentCommand($commandOrDocumentId, $password);
        } else {
            Assert::isInstanceOf($commandOrDocumentId, SignDocumentCommand::class);
            $command = $commandOrDocumentId;
        }

        try {
            $this->logger->info(
                'Signing document: {documentId}, attempt: {attempt}',
                [
                    'documentId' => $command->documentId,
                    'attempt' => Activity::getInfo()->attempt,
                ],
            );

            // schedule-to-close has never been elapsed yet
            if (Activity::getInfo()->attempt < 2) {
                throw new LogicException('oops');
            }

            $result = $command->process();

            $this->logger->info(
                'Document signed successfully: {documentId}, attempt: {attempt}, result: {result}',
                [
                    'documentId' => $command->documentId,
                    'attempt' => Activity::getInfo()->attempt,
                    'result' => $result,
                ],
            );

            return $result;
        } catch (Exception $e) {
            $violationList = $this->exceptionMapper->map($command, $e);

            if (null === $violationList) {
                throw $e;
            }

            throw new ApplicationFailure(
                'Validation Failed',
                'validation',
                true,
                EncodedValues::fromValues(iterator_to_array($violationList)),
                previous: $e,
            );
        }
    }

    #[ActivityMethod]
    public function cancel(SignDocumentCommand $command): void
    {
        $this->logger->info(
            'Cancelled document: {documentId}',
            ['documentId' => $command->documentId],
        );
    }
}
