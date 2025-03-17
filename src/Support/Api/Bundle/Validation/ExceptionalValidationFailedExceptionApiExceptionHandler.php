<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\Validation;

use PhPhD\ExceptionalValidation\Handler\Exception\ExceptionalValidationFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler(bus: 'api.exception.bus')]
final readonly class ExceptionalValidationFailedExceptionApiExceptionHandler
{
    public function __construct(
        private TranslatorInterface $translator,
        private ViolationListApiResponseFormatter $violationsFormatter,
    ) {
    }

    public function __invoke(ExceptionalValidationFailedException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'validation_failed',
            'errorDescription' => $this->translator->trans('app_api.error.validation_failed'),
            'violations' => $this->violationsFormatter->format($exception->getViolationList()),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
