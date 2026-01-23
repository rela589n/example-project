<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait {
        registerBundles as private registerThirdPartyBundles;
    }

    public function registerBundles(): iterable
    {
        /** @phpstan-ignore generator.valueType */
        yield from $this->registerThirdPartyBundles();

        yield new Support\Contracts\Bundle\AppContractsBundle();

        yield new Support\MessageBus\Bundle\AppMessageBusBundle();

        yield new Support\CycleBridge\Bundle\AppCycleBridgeBundle();

        yield new Support\Doctrine\Bundle\AppDoctrineBundle();

        yield new Support\Api\Bundle\AppApiBundle();

        yield new Support\Logging\Bundle\AppLoggingBundle();

        yield new Support\Temporal\Bundle\AppTemporalBundle();

        yield new Support\Partitioning\Bundle\AppPartitioningBundle();

        yield new Infra\WebSocket\Bundle\AppWebSocketBundle();

        yield new Playground\Bundle\AppPlaygroundBundle();

        yield new EmployeePortal\Authentication\Bundle\AppAuthBundle();

        // yield new EmployeePortal\Accounting\Bundle\AppAccountingBundle();

        yield new EmployeePortal\Voucher\Bundle\AppVoucherBundle();

        yield new EmployeePortal\Entity\Bundle\AppEntityBundle();

        yield new EmployeePortal\Blog\Support\Bundle\AppBlogBundle();

        yield new EmployeePortal\Chatbot\Support\Bundle\AppChatbotBundle();
    }
}
