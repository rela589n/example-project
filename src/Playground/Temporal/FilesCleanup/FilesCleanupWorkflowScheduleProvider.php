<?php

declare(strict_types=1);

namespace App\Playground\Temporal\FilesCleanup;

use App\Support\Temporal\Schedule\ScheduleProvider;
use Carbon\CarbonInterval;
use Temporal\Client\Schedule\Action\StartWorkflowAction;
use Temporal\Client\Schedule\Schedule;
use Temporal\Client\Schedule\Spec\IntervalSpec;
use Temporal\Client\Schedule\Spec\ScheduleSpec;

final readonly class FilesCleanupWorkflowScheduleProvider implements ScheduleProvider
{
    public function getSchedule(): Schedule
    {
        return Schedule::new()
            ->withAction(
                StartWorkflowAction::new(FilesCleanupWorkflow::TYPE),
            )
            ->withSpec(
                ScheduleSpec::new()
                    ->withIntervalList(
                        IntervalSpec::new(
                            CarbonInterval::hours(3),
                        ),
                    ),
            )
        ;
    }

    public static function getId(): string
    {
        return '3748330f-e51d-7254-ac4d-be07eb3cf96b';
    }
}
