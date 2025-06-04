<?php

declare(strict_types=1);

namespace App\Support\Temporal\Schedule;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(ScheduleProviderTombstone::class)]
interface ScheduleProviderTombstone
{
    public static function getId(): string;
}
