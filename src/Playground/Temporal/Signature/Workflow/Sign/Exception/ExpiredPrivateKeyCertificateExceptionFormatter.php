<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign\Exception;

use PhPhD\ExceptionalValidation\Mapper\Validator\Formatter\Item\ExceptionViolationFormatter;
use PhPhD\ExceptionalValidation\Rule\Exception\CapturedException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\ConstraintViolation;

/** @implements ExceptionViolationFormatter<ExpiredPrivateKeyCertificateException> */
final readonly class ExpiredPrivateKeyCertificateExceptionFormatter implements ExceptionViolationFormatter
{
    public function __construct(
        #[Autowire('@phd_exceptional_validation.violation_formatter.default')]
        private ExceptionViolationFormatter $formatter,
    ) {
    }

    /** @param CapturedException<ExpiredPrivateKeyCertificateException> $capturedException */
    public function format(CapturedException $capturedException): array
    {
        $exception = $capturedException->getException();

        [$violation] = $this->formatter->format($capturedException);

        return [
            new ConstraintViolation(
                $violation->getMessage(),
                $violation->getMessageTemplate(),
                array_merge($violation->getParameters(), ['expiration_date' => $exception->expirationDate->toAtomString()]),
                $violation->getRoot(),
                $violation->getPropertyPath(),
                $violation->getInvalidValue(),
            ),
        ];
    }
}
