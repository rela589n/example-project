<?php

declare(strict_types=1);

namespace App\Support\Logging\Context;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

#[AsMonologProcessor]
final readonly class ExceptionContextLogProcessor
{
    private const array IGNORED_ATTRIBUTES = ['code', 'trace', 'traceAsString'];

    public function __construct(
        #[Autowire('@serializer')]
        private NormalizerInterface $serializer,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $context = $record->context;

        if (!isset($context['exception'])) {
            return $record;
        }

        $exception = $context['exception'];

        if (!$exception instanceof Throwable) {
            return $record;
        }

        if (str_contains($exception->getFile(), '/vendor/')) {
            return $record;
        }

        $exceptionContext = $this->serializer->normalize($exception, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => self::IGNORED_ATTRIBUTES,
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ]);

        $context['caught'] = $exceptionContext;

        return $record->with(context: $context);
    }
}
