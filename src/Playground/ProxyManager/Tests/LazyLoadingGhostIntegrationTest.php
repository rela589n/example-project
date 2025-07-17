<?php

declare(strict_types=1);

namespace App\Playground\ProxyManager\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(LazyLoadingGhostFactory::class)]
final class LazyLoadingGhostIntegrationTest extends KernelTestCase
{
    private LazyLoadingGhostFactory $ghostFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        /** @var LazyLoadingGhostFactory $ghostFactory */
        $ghostFactory = $container->get('app_proxy_manager.factory.lazy_loading_ghost');
        $this->ghostFactory = $ghostFactory;
    }

    public function testHookedPropertiesAreProblematic(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot unset hooked property ProxyManagerGeneratedProxy\Generatedfcde5888318403a5033f7bf87317fd2b\__PM__\App\Playground\ProxyManager\Tests\ProxiedObject::$foo');

        $this->ghostFactory->createProxy(
            ProxiedObject::class,
            function () {
            },
        );
    }
}
