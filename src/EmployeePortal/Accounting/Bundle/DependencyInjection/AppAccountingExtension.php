<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Bundle\DependencyInjection;

use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AppAccountingExtension extends AbstractExtension
{
    public const ALIAS = 'app_accounting';

    /** @param array<array-key,mixed> $config */
    #[Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/services.yaml');
    }

    #[Override]
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/config/packages/*.yaml');
    }

    #[Override]
    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
