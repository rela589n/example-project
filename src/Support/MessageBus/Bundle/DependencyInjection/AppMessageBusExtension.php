<?php

declare(strict_types=1);

namespace App\Support\MessageBus\Bundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AppMessageBusExtension extends AbstractExtension
{
    public const ALIAS = 'app_message_bus';

    /**
     * @override
     *
     * @throws Exception
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/services.yaml');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/../../**/config/packages/*.yaml');
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
