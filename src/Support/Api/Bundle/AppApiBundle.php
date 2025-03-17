<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle;

use App\Support\Api\Bundle\DependencyInjection\AppApiExtension;
use App\Support\Api\Bundle\RequestMapping\Serializer\RequestPayloadSerializerCompilerPass;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppApiBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppApiExtension
    {
        return new AppApiExtension();
    }

    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RequestPayloadSerializerCompilerPass());
    }
}
