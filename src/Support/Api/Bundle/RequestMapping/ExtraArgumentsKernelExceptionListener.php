<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\RequestMapping;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: 'kernel.exception', method: '__invoke')]
final readonly class ExtraArgumentsKernelExceptionListener
{
    public function __construct(
        #[Autowire(lazy: true)]
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ExtraAttributesException) {
            return;
        }

        $extraArguments = $exception->getExtraAttributes();

        $response = new JsonResponse([
            'error' => 'extra_arguments',
            'errorDescription' => $this->translator->trans('app_api.error.extra_arguments'),
            'extraArguments' => $extraArguments,
        ], Response::HTTP_BAD_REQUEST);

        $event->setResponse($response);
    }
}
