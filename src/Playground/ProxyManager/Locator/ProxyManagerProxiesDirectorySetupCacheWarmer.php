<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Locator;

use ProxyManager\Configuration as ProxyManagerConfiguration;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use function is_dir;
use function is_writable;
use function mkdir;
use function sprintf;

#[AsAlias('app_proxy_manager.proxy_cache_warmer')]
#[AutoconfigureTag('kernel.cache_warmer')]
final readonly class ProxyManagerProxiesDirectorySetupCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        #[Autowire('@app_proxy_manager.config')]
        private ProxyManagerConfiguration $configuration,
    ) {
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $proxyCacheDir = $this->configuration->getProxiesTargetDir();

        if (!is_dir($proxyCacheDir)) {
            if (!mkdir($proxyCacheDir, 0775, true) && !is_dir($proxyCacheDir)) {
                throw new RuntimeException(
                    sprintf('Unable to create the Proxy Manager directory "%s".', $proxyCacheDir),
                );
            }
        } elseif (!is_writable($proxyCacheDir)) {
            throw new RuntimeException(
                sprintf('The Proxy Manager directory "%s" is not writeable for the current system user.', $proxyCacheDir),
            );
        }

        return [];
    }
}
