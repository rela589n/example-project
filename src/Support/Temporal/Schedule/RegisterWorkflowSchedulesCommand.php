<?php

declare(strict_types=1);

namespace App\Support\Temporal\Schedule;

use Temporal\Client\Schedule\Info\ScheduleListEntry;
use Temporal\Client\ScheduleClientInterface;

use function array_keys;
use function array_map;
use function implode;
use function sprintf;

final class RegisterWorkflowSchedulesCommand
{
    public function __construct(
        /** @var list<non-empty-string> */
        private readonly array $deletedScheduleIds,
        /** @var array<non-empty-string,ScheduleProvider> */
        private array $scheduleProviders,
    ) {
    }

    public function execute(ScheduleClientInterface $scheduleClient): void
    {
        $this->deleteSchedules($scheduleClient);

        $this->updateSchedules($scheduleClient);

        $this->createSchedules($scheduleClient);
    }

    private function deleteSchedules(ScheduleClientInterface $scheduleClient): void
    {
        foreach ($this->getSchedulesByIds($scheduleClient, $this->deletedScheduleIds) as $scheduleListEntry) {
            $this->deleteSchedule($scheduleClient, $scheduleListEntry);
        }
    }

    private function deleteSchedule(ScheduleClientInterface $scheduleClient, ScheduleListEntry $scheduleListEntry): void
    {
        /** @var non-empty-string $scheduleId */
        $scheduleId = $scheduleListEntry->scheduleId;

        $scheduleHandle = $scheduleClient->getHandle($scheduleId);

        $scheduleHandle->delete();
    }

    private function updateSchedules(ScheduleClientInterface $scheduleClient): void
    {
        foreach ($this->getSchedulesByIds($scheduleClient, array_keys($this->scheduleProviders)) as $scheduleListEntry) {
            $this->updateSchedule($scheduleListEntry, $scheduleClient);

            unset($this->scheduleProviders[$scheduleListEntry->scheduleId]);
        }
    }

    private function updateSchedule(ScheduleListEntry $scheduleListEntry, ScheduleClientInterface $scheduleClient): void
    {
        /** @var non-empty-string $scheduleId */
        $scheduleId = $scheduleListEntry->scheduleId;

        $scheduleHandle = $scheduleClient->getHandle($scheduleId);

        $schedule = $this->scheduleProviders[$scheduleId]->getSchedule();

        $scheduleHandle->update($schedule);
    }

    private function createSchedules(ScheduleClientInterface $scheduleClient): void
    {
        foreach (array_keys($this->scheduleProviders) as $scheduleId) {
            $this->createSchedule($scheduleId, $scheduleClient);

            unset($this->scheduleProviders[$scheduleId]);
        }
    }

    /** @param non-empty-string $scheduleId */
    private function createSchedule(string $scheduleId, ScheduleClientInterface $scheduleClient): void
    {
        $schedule = $this->scheduleProviders[$scheduleId]->getSchedule();

        $scheduleClient->createSchedule($schedule, scheduleId: $scheduleId);
    }

    /**
     * @param list<string> $scheduleIds
     *
     * @return iterable<ScheduleListEntry>
     */
    private function getSchedulesByIds(ScheduleClientInterface $scheduleClient, array $scheduleIds): iterable
    {
        if ([] === $scheduleIds) {
            return [];
        }

        return $scheduleClient->listSchedules(
            query: sprintf(
                'ScheduleId IN (%s)',
                implode(
                    ',',
                    array_map(
                        static fn (string $id): string => sprintf('"%s"', $id),
                        $scheduleIds,
                    ),
                ),
            ),
        );
    }
}
