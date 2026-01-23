<?php

declare(strict_types=1);

namespace App\EmployeePortal\Chatbot\Support\Bundle;

use App\EmployeePortal\Chatbot\Support\Bundle\DependencyInjection\AppChatbotExtension;
use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AppChatbotBundle extends Bundle
{
    #[Override]
    protected function createContainerExtension(): AppChatbotExtension
    {
        return new AppChatbotExtension();
    }
}
