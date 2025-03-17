<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\Validation;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use function array_map;

final readonly class ViolationListApiResponseFormatter
{
    public function __construct(
        #[Autowire(lazy: true)]
        private NormalizerInterface $apiNormalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     *
     * @return list<array{propertyPath: mixed, title: mixed}>
     */
    public function format(ConstraintViolationListInterface $violationList): array
    {
        /** @var array{violations:list<array{propertyPath: mixed, title: mixed}>} $result */
        $result = $this->apiNormalizer->normalize($violationList);

        return array_map(
            static fn (array $violation): array => [
                'propertyPath' => $violation['propertyPath'],
                'title' => $violation['title'],
            ],
            $result['violations'],
        );
    }
}
