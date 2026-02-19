<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\_Features\Create\Port;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\SequenceGenerator;
use Psr\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateAccountService
{
    private const string SEQ_NAME = 'accounting_accounts_number_seq';

    private SequenceGenerator $sequenceGenerator;

    public function __construct(
        public ClockInterface $clock,
        public EntityManagerInterface $entityManager,
        #[Autowire('@event.bus')]
        public MessageBusInterface $eventBus,
    ) {
        $this->sequenceGenerator = new SequenceGenerator(self::SEQ_NAME, 1);
    }

    public function __invoke(CreateAccountCommand $command): void
    {
        $command->process($this);
    }

    public function generateNumber(): int
    {
        return $this->sequenceGenerator->generateId($this->entityManager, null);
    }

    public function now(): CarbonImmutable
    {
        return CarbonImmutable::instance($this->clock->now());
    }
}
