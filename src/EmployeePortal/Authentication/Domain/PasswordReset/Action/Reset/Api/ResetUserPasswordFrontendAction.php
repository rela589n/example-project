<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Api;

use App\EmployeePortal\Authentication\Domain\PasswordReset\Action\Reset\Handler\ResetUserPasswordCommand;
use OpenApi\Attributes as OA;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[OA\Post(
    summary: 'Reset password'
)]
#[OA\Response(
    response: 200,
    description: 'OK'
)]
#[AsController]
final readonly class ResetUserPasswordFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
    ) {
    }

    #[Route(
        path: '/reset-password',
        name: 'employee_portal_auth_user_reset_password',
        methods: ['POST']
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

        $envelope = $this->apiBus->dispatch($command, [/*new BusNameStamp('command.bus')*/]);

        /** @var ?HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var ?Response $result */
        $result = $handled->getResult();

        return $result ?? new Response(status: 200);
    }
}
