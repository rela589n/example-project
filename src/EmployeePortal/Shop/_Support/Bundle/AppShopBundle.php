<?php

declare(strict_types=1);

namespace App\EmployeePortal\Shop\_Support\Bundle;

use App\EmployeePortal\Shop\_Support\Bundle\DependencyInjection\AppShopExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppShopBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppShopExtension
    {
        return new AppShopExtension();
    }
}
