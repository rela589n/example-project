<?php

declare(strict_types=1);

namespace App\EmployeePortal\AuthBundle\Domain\User\Register\Http;

use App\EmployeePortal\AuthBundle\Domain\User\Register\Exception\EmailAlreadyTakenException;
use App\EmployeePortal\AuthBundle\Domain\User\Register\Handler\RegisterUserCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class RegisterUserFrontendAction
{
    public function __construct(
        #[Autowire('@api.bus')]
        private MessageBusInterface $apiBus,
        #[Autowire('@command.bus')]
        private MessageBusInterface $commandBus,
    ) {
    }

    #[Route('/register')]
    public function __invoke(
        #[MapRequestPayload]
        RegisterUserCommand $command,
    ): Response {
        $envelope = $this->apiBus->dispatch($command);

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        /** @var Response $result */
        $result = $handled->getResult();

        return $result;
    }

    #[AsMessageHandler(bus: 'api.bus')]
    public function handle(RegisterUserCommand $command): Response
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (EmailAlreadyTakenException) {
            // Some domain exceptions (scoped to this particular scenario) could be caught and formatted into the response:

            return new JsonResponse([
                'error' => 'email_already_taken',
                'errorDescription' => 'Email is already taken',
            ], 400);
        }

        return new Response(status: 201);
    }
}
