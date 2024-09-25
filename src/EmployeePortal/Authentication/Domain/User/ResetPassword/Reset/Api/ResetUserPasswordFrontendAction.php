<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\User\ResetPassword\Reset\Api;

use App\EmployeePortal\Authentication\Domain\User\ResetPassword\Reset\Handler\ResetUserPasswordCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final readonly class ResetUserPasswordFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
        #[Autowire('@command.bus')]
        private MessageBusInterface $commandBus,
    ) {
    }

    #[Route(
        path: '/reset-password',
        methods: ['POST'],
    )]
    public function __invoke(
        #[CurrentUser]
        UserInterface $user,
        Request $request,
    ): Response {
        $command = new ResetUserPasswordCommand(
            $user->getUserIdentifier(),
            $request->request->getString('passwordResetRequestId'),
        );

        $envelope = $this->apiBus->dispatch($command);

        /** @var ?HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var Response $result */
        $result = $handled->getResult();

        return $result;
    }

    #[AsMessageHandler(bus: 'api.bus')]
    public function handle(ResetUserPasswordCommand $command): Response
    {
        $this->commandBus->dispatch($command);

        return new Response(status: 200);
    }
}
