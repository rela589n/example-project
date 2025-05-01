<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\RequestMapping\Serializer;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class RequestPayloadSerializer implements SerializerInterface, DenormalizerInterface
{
    public function __construct(
        #[Autowire('@serializer')]
        private SerializerInterface&DenormalizerInterface $serializer,
    ) {
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context += [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            AbstractNormalizer::REQUIRE_ALL_PROPERTIES => true,
        ];

        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        $context += [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            AbstractNormalizer::REQUIRE_ALL_PROPERTIES => true,
        ];

        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $this->serializer->supportsDenormalization($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->serializer->getSupportedTypes($format);
    }
}
