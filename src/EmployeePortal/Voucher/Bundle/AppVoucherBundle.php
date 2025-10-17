<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Bundle;

use App\EmployeePortal\Voucher\Bundle\DependencyInjection\AppVoucherExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppVoucherBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppVoucherExtension
    {
        return new AppVoucherExtension();
    }
}
