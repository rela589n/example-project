<?php

declare(strict_types=1);

namespace App\Support\MessageBus\Bundle;

use App\Support\MessageBus\Bundle\DependencyInjection\AppMessageBusExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppMessageBusBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppMessageBusExtension
    {
        return new AppMessageBusExtension();
    }
}
