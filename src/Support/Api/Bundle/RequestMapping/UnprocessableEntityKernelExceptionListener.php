<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\RequestMapping;

use App\Support\Api\Bundle\Validation\ViolationListApiResponseFormatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

#[AsEventListener(event: 'kernel.exception', method: '__invoke')]
final readonly class UnprocessableEntityKernelExceptionListener
{
    public function __construct(
        private ViolationListApiResponseFormatter $violationsFormatter,
        #[Autowire(lazy: true)]
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof UnprocessableEntityHttpException) {
            return;
        }

        /** @var ValidationFailedException|object $validationFailedException */
        $validationFailedException = $exception->getPrevious();
        Assert::isInstanceOf($validationFailedException, ValidationFailedException::class);

        $response = new JsonResponse([
            'error' => 'schema_validation_failed',
            'errorDescription' => $this->translator->trans('app_api.error.validation_failed'),
            'violations' => $this->violationsFormatter->format($validationFailedException->getViolations()),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
    }
}
