<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign\Exception;

use PhPhD\ExceptionalMatcher\Rule\Exception\MatchedException;
use PhPhD\ExceptionalMatcher\Validator\Formatter\ExceptionViolationFormatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\ConstraintViolation;

use function array_merge;

/** @implements ExceptionViolationFormatter<ExpiredPrivateKeyCertificateException> */
final readonly class ExpiredPrivateKeyCertificateExceptionFormatter implements ExceptionViolationFormatter
{
    public function __construct(
        #[Autowire(service: ExceptionViolationFormatter::class.'<Throwable>')]
        private ExceptionViolationFormatter $formatter,
    ) {
    }

    /** @param MatchedException<ExpiredPrivateKeyCertificateException> $matchedException */
    public function format(MatchedException $matchedException): array
    {
        $exception = $matchedException->getException();

        [$violation] = $this->formatter->format($matchedException);

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
