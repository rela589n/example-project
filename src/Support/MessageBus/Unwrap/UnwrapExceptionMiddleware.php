<?php

declare(strict_types=1);

namespace App\Support\MessageBus\Unwrap;

use PhPhD\ExceptionToolkit\Unwrapper\ExceptionUnwrapper;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

use function count;

#[AsAlias('app_unwrap_exception')]
final readonly class UnwrapExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        #[Autowire('@phd_exception_toolkit.exception_unwrapper')]
        private ExceptionUnwrapper $exceptionUnwrapper,
    ) {
    }

    /**
     * @throws Throwable
     *
     * @throws ExceptionInterface
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Throwable $exception) {
            $elementaryExceptions = $this->exceptionUnwrapper->unwrap($exception);

            if (1 !== count($elementaryExceptions)) {
                throw $exception;
            }

            [$elementaryException] = $elementaryExceptions;

            throw $elementaryException;
        }
    }
}
