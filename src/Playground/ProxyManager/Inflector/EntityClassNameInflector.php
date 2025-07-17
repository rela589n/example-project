<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Inflector;

use ProxyManager\Inflector\ClassNameInflectorInterface;
use ProxyManager\Inflector\Util\ParameterHasher;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function is_int;
use function ltrim;
use function strlen;
use function strrpos;
use function substr;

/**
 * This inflector class is compatible with doctrine proxies namespaces,
 * which imply that the original class name will be right after _CG_ marker.
 */
#[AsAlias('app_proxy_manager.config.class_name_inflector')]
final class EntityClassNameInflector implements ClassNameInflectorInterface
{
    private string $proxyNamespace;

    private string $proxyMarker;

    private int $proxyMarkerLength;

    private ParameterHasher $parameterHasher;

    public function __construct(
        #[Autowire('@=service("app_proxy_manager.config").getProxiesNamespace()')]
        string $proxyNamespace,
    ) {
        $this->proxyNamespace = $proxyNamespace;
        $this->proxyMarker = '\\'.self::PROXY_MARKER.'\\';
        $this->proxyMarkerLength = strlen($this->proxyMarker);
        $this->parameterHasher = new ParameterHasher();
    }

    public function getUserClassName(string $className): string
    {
        $className = ltrim($className, '\\');
        $position = strrpos($className, $this->proxyMarker);

        if (!is_int($position)) {
            /** @psalm-suppress LessSpecificReturnStatement */
            return $className;
        }

        /** @psalm-suppress LessSpecificReturnStatement */
        return substr($className, $this->proxyMarkerLength + $position);
    }

    public function getProxyClassName(string $className, array $options = []): string
    {
        /** @psalm-suppress LessSpecificReturnStatement */
        return $this->proxyNamespace
            .'\\Generated'.$this->parameterHasher->hashParameters($options)
            .$this->proxyMarker
            .$this->getUserClassName($className);
    }

    public function isProxyClassName(string $className): bool
    {
        return false !== strrpos($className, $this->proxyMarker);
    }
}
