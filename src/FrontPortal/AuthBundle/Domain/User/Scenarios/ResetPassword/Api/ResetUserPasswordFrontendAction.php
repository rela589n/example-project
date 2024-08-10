<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\Api;

use App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\ResetUserPasswordCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsController]
final readonly class ResetUserPasswordFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload]
        ResetUserPasswordCommand $command,
    ): mixed {
        $envelope = $this->apiBus->dispatch($command, [new BusNameStamp('command.bus')]);

        /** @var ?HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult() ?? new Response(status: 200);
    }
}
