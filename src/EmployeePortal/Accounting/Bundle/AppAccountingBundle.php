<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Bundle;

use App\EmployeePortal\Accounting\Bundle\DependencyInjection\AppAccountingExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppAccountingBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppAccountingExtension
    {
        return new AppAccountingExtension();
    }
}
