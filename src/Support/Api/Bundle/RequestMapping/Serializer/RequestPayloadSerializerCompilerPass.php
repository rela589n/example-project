<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\RequestMapping\Serializer;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final readonly class RequestPayloadSerializerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $requestPayloadArgumentResolver = $container->getDefinition('argument_resolver.request_payload');

        $container->setDefinition(
            RequestPayloadSerializer::class,
            new Definition(RequestPayloadSerializer::class, [$requestPayloadArgumentResolver->getArgument(0)])
        );

        $requestPayloadArgumentResolver->replaceArgument(0, new Reference(RequestPayloadSerializer::class));
    }
}
