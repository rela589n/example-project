<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Autoloader;

use Composer\Autoload\ClassLoader;
use LogicException;
use Webmozart\Assert\Assert;

final class EntityAutoloader
{
    /** @var array<class-string, string> */
    private array $proxyLocations = [
        AnEntity::class => __DIR__.'/EntityProxy.php',
    ];

    public function __construct(
        private readonly ClassLoader $composerLoader,
    ) {
    }

    public static function create(string $vendorSuffix = '/vendor'): self
    {
        return new self(self::getComposerLoader($vendorSuffix));
    }

    private static function getComposerLoader(string $vendorSuffix): ClassLoader
    {
        /** @var non-empty-array<string,ClassLoader> $loaders */
        $loaders = ClassLoader::getRegisteredLoaders();

        foreach ($loaders as $dir => $loader) {
            if (str_ends_with($dir, $vendorSuffix)) {
                break;
            }

            $loader = null;
        }

        return $loader ?? throw new LogicException('Composer Loader not found');
    }

    public function register(bool $prepend = true): void
    {
        spl_autoload_register([$this, 'loadClass'], true, $prepend);
    }

    public function unregister(): void
    {
        spl_autoload_unregister([$this, 'loadClass']);
    }

    public function loadClass(string $className): void
    {
        // how do I know whether it's proxied?

        if (!$this->isProxied($className)) {
            return;
        }

        $this->declareOriginalClass($className);

        require $this->getProxyLocation($className);
    }

    public function isProxied(string $className): bool
    {
        return isset($this->proxyLocations[$className]);
    }

    private function getProxyLocation(string $className): string
    {
        return $this->proxyLocations[$className];
    }

    private function declareOriginalClass(string $className): void
    {
        $file = $this->composerLoader->findFile($className);
        Assert::string($file, 'Could not find file for class '.$className);

        $source = file_get_contents($file);
        Assert::string($source, 'Could not read file contents for '.$file);

        $trimmed = substr($source, strlen('<?php'));
        $originalClassCode = preg_replace('/\bclass\s+(\w+)/', 'class ${1}Original', $trimmed, 1);
        eval($originalClassCode);
    }
}
