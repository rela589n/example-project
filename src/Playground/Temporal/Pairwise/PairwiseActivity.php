<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Pairwise;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface('Pairwise.')]
#[AssignWorker('default')]
final class PairwiseActivity
{
    #[ActivityMethod]
    public function call(string $input): string
    {
        return $input;
    }
}

