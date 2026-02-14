<?php

declare(strict_types=1);

namespace App\Playground\Bundle;

use App\Playground\Autowire\Iterator\Vat\CompilerPass\VatServiceCompilerPass;
use App\Playground\Bundle\DependencyInjection\AppPlaygroundExtension;
use Override;
use ProxyManager\Configuration as ProxyManagerConfiguration;
use ProxyManager\Exception\InvalidProxyDirectoryException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function spl_autoload_register;

final class AppPlaygroundBundle extends Bundle
{
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new VatServiceCompilerPass());
    }

    #[Override]
    public function boot(): void
    {
        parent::boot();

        /** @var ProxyManagerConfiguration $config */
        $config = $this->container->get('app_proxy_manager.config'); // @phpstan-ignore method.nonObject

        try {
            spl_autoload_register($config->getProxyAutoloader()); // @phpstan-ignore argument.type
        } catch (InvalidProxyDirectoryException) {
            $config->setGeneratorStrategy($this->container->get('app_proxy_manager.generator_strategy.fallback')); // @phpstan-ignore method.nonObject
        }
    }

    #[Override]
    protected function createContainerExtension(): AppPlaygroundExtension
    {
        return new AppPlaygroundExtension();
    }
}
