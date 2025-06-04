<?php

declare(strict_types=1);

namespace App\Support\Temporal\Schedule;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Temporal\Client\Schedule\Schedule;

#[AutoconfigureTag(ScheduleProvider::class)]
interface ScheduleProvider
{
    public static function getId(): string;

    public function getSchedule(): Schedule;
}
