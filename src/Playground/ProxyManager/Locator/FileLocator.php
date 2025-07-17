<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Locator;

use ProxyManager\FileLocator\FileLocatorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function mkdir;
use function realpath;

#[AsAlias('app_proxy_manager.file_locator')]
final class FileLocator implements FileLocatorInterface
{
    private \ProxyManager\FileLocator\FileLocator $fileLocator;

    public function __construct(
        #[Autowire('@=service("app_proxy_manager.config").getProxiesTargetDir()')]
        string $proxiesDirectory,
    ) {
        $absolutePath = realpath($proxiesDirectory);

        if (false === $absolutePath) {
            /** @noinspection MkdirRaceConditionInspection */
            @mkdir($proxiesDirectory, 0775, true);
        }

        $this->fileLocator = new \ProxyManager\FileLocator\FileLocator($proxiesDirectory);
    }

    public function getProxyFileName(string $className): string
    {
        return $this->fileLocator->getProxyFileName($className);
    }
}
