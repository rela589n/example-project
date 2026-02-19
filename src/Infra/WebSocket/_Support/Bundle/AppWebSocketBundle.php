<?php

declare(strict_types=1);

namespace App\Infra\WebSocket\_Support\Bundle;

use App\Infra\WebSocket\_Support\Bundle\DependencyInjection\AppWebSocketExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppWebSocketBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppWebSocketExtension
    {
        return new AppWebSocketExtension();
    }
}
