<?php

declare(strict_types=1);

namespace App\EmployeePortal\Service\Bundle;

use App\EmployeePortal\Service\Bundle\DependencyInjection\AppServiceExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppServiceBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppServiceExtension
    {
        return new AppServiceExtension();
    }
}
