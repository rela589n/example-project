<?php

declare(strict_types=1);

namespace App\Support\Temporal\Schedule;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Temporal\Client\ScheduleClientInterface;

#[AsCommand('app:temporal:schedules:register', description: 'Register Workflow Schedules')]
final class RegisterWorkflowSchedulesConsoleCommand extends Command
{
    public function __construct(
        #[Autowire('@temporal.default.schedule_client')]
        private readonly ScheduleClientInterface $scheduleClient,
        /** @var iterable<non-empty-string,ScheduleProviderTombstone> */
        #[AutowireIterator(ScheduleProviderTombstone::class, defaultIndexMethod: 'getId')]
        private readonly iterable $scheduleProviderTombstones,
        /** @var iterable<non-empty-string,ScheduleProvider> */
        #[AutowireIterator(ScheduleProvider::class, defaultIndexMethod: 'getId')]
        private readonly iterable $scheduleProviders,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deletedScheduleIds = array_keys(iterator_to_array($this->scheduleProviderTombstones));
        $deletedCount = count($deletedScheduleIds);

        $scheduleProviders = iterator_to_array($this->scheduleProviders);
        $count = count($scheduleProviders);

        if (0 === $deletedCount && 0 === $count) {
            $io->warning('No workflow schedules found to register.');

            return Command::SUCCESS;
        }

        $command = new RegisterWorkflowSchedulesCommand($deletedScheduleIds, $scheduleProviders);

        $command->execute($this->scheduleClient);

        $io->success(sprintf(
            'Successfully registered %d workflow schedules and deleted %d tombstone schedules.',
            $count,
            $deletedCount,
        ));

        return Command::SUCCESS;
    }
}
