<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Register\Http;

use App\FrontPortal\AuthBundle\Domain\User\Register\Handler\RegisterUserCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class RegisterUserFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route('/register')]
    public function __invoke(
        #[MapRequestPayload]
        RegisterUserCommand $command,
    ): mixed {
        $envelope = $this->apiBus->dispatch($command, [new BusNameStamp('command.bus')]);

        /** @var ?HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult() ?? new Response(status: 201);
    }
}
