<?php

declare(strict_types=1);

namespace App\Playground\Autowire\Iterator\Vat\CompilerPass;

use App\Playground\Autowire\Iterator\Vat\VatCalculatorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_keys;
use function array_map;
use function uasort;

final readonly class VatServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<string,list<array{priority:int}>> $taggedServiceIds */
        $taggedServiceIds = $container->findTaggedServiceIds('app.vat_provider');

        uasort($taggedServiceIds, static fn (array $tags1, array $tags2): int => $tags2[0]['priority'] <=> $tags1[0]['priority']);

        $definition = $container->getDefinition(VatCalculatorService::class);

        $serviceIds = array_keys($taggedServiceIds);
        $serviceReferences = array_map(static fn (string $id): Reference => new Reference($id), $serviceIds);

        $definition->setArgument(0, $serviceReferences);
    }
}
