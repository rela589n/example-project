<?php

declare(strict_types=1);

namespace App\Playground\Temporal\VersionedWorld;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('VersionedActivity.')]
#[AssignWorker('default')]
final class HelloWorldActivity
{
    #[ActivityMethod]
    public function hello(): string
    {
        return 'Hello';
    }

    #[ActivityMethod]
    public function versioned(): string
    {
        return 'versioned';
    }

    #[ActivityMethod]
    public function world(): string
    {
        return 'World';
    }
}
