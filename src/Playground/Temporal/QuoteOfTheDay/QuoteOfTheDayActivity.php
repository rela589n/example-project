<?php

declare(strict_types=1);

namespace App\Playground\Temporal\QuoteOfTheDay;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Vanta\Integration\Symfony\Temporal\Attribute\AssignWorker;

#[ActivityInterface]
#[AssignWorker('default')]
final readonly class QuoteOfTheDayActivity
{
    private const array QUOTES = [
        'Conscience is the voice of your spirit',
        'Reasoning is the voice of your soul',
        'Feelings is the voice of the body',
    ];

    #[ActivityMethod]
    public function getQuoteOfTheDay(int $day): string
    {
        return self::QUOTES[$day % count(self::QUOTES)];
    }
}
