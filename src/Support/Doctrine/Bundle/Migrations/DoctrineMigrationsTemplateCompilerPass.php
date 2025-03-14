<?php

declare(strict_types=1);

namespace App\Support\Doctrine\Bundle\Migrations;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

final readonly class DoctrineMigrationsTemplateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $configurationDefinition = $container->getDefinition('doctrine.migrations.configuration');

        $methodCall = $this->getCustomTemplateMethodCall($configurationDefinition);

        if (null === $methodCall) {
            return;
        }

        [$customTemplate] = $methodCall[1];

        if (!str_starts_with($customTemplate, '@=')) {
            return;
        }

        $configurationDefinition->removeMethodCall('setCustomTemplate');
        $configurationDefinition->addMethodCall('setCustomTemplate', [new Expression(substr($customTemplate, 2))]);
    }

    /** @return ?array{string,array} */
    private function getCustomTemplateMethodCall(Definition $configurationDefinition): ?array
    {
        /** @var array{string,array} $call */
        foreach ($configurationDefinition->getMethodCalls() as $call) {
            $methodName = $call[0];

            if ('setCustomTemplate' === $methodName) {
                return $call;
            }
        }

        return null;
    }
}
