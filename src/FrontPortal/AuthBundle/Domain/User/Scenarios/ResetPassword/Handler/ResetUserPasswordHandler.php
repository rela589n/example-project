<?php

declare(strict_types=1);

namespace App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\Handler;

use Amp\Future;
use App\FrontPortal\AuthBundle\Domain\User\Entity\PasswordResetRequest;
use App\FrontPortal\AuthBundle\Domain\User\Exception\PasswordResetRequestNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Exception\UserNotFoundException;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\ResetUserPasswordCommand;
use App\FrontPortal\AuthBundle\Domain\User\Scenarios\ResetPassword\UserPasswordResetEvent;
use App\FrontPortal\AuthBundle\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

use function Amp\async;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ResetUserPasswordHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $event = UserPasswordResetEvent::of(
            $this->getUser($command),
            $this->getPasswordResetRequest($command),
        );

        // event.process() is called within event.bus middleware
        $this->eventBus->dispatch($event);

        $this->entityManager->persist($event);
    }

    private function getUser(ResetUserPasswordCommand $command): Future
    {
        return async(fn (): User => $this->findUser($command));
    }

    private function getPasswordResetRequest(ResetUserPasswordCommand $command): Future
    {
        return async(fn (): PasswordResetRequest => $this->findPasswordResetRequest($command));
    }

    private function findUser(ResetUserPasswordCommand $command): User
    {
        $id = Uuid::fromString($command->getUserId());

        return $this->entityManager->find(User::class, $id)
            ?? throw new UserNotFoundException(id: $id);
    }

    private function findPasswordResetRequest(ResetUserPasswordCommand $command): PasswordResetRequest
    {
        $id = Uuid::fromString($command->getPasswordResetRequestId());

        return $this->entityManager->find(PasswordResetRequest::class, $id)
            ?? throw new PasswordResetRequestNotFoundException($id);
    }
}
