<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\DailyTemperatures;

use SplStack;

use function array_fill;
use function count;

/** @see https://leetcode.com/problems/daily-temperatures/description/ */
final readonly class DailyTemperaturesSolution
{
    /**
     * @param list<int> $temperatures
     *
     * @return array<int,int>
     */
    public function dailyTemperatures(array $temperatures): array
    {
        // [73,74,75,71,69,72,76,73]
        // [73] - stack
        //    74 - solution to 73, drop 73 from stack
        //    [74] - push 74 to stack
        //       75 - solution to 74, drop 74 from stack
        //       [75] - push 75 to stack
        //       [75,71] - push 71 to the stack
        //       [75,71,69] - push 69 to the stack
        //                 72 - solution to 69, and to 71
        //       [75,      72] - add 72
        //                     76 - solution to both 75, 72
        //                    [76] - add 76 to stack
        //                    [76,73] - add 73 to the stack

        /** @var SplStack<int> $pendingBetterWeatherDays */
        $pendingBetterWeatherDays = new SplStack();

        $n = count($temperatures);

        /** @var list<int> $results */
        $results = array_fill(0, $n, 0);

        foreach ($temperatures as $day => $dayTemperature) {
            while (!$pendingBetterWeatherDays->isEmpty()) {
                $pendingBetterWeatherDay = $pendingBetterWeatherDays->top();

                if ($dayTemperature <= $temperatures[$pendingBetterWeatherDay]) {
                    // Current weather is no better.
                    // No point looking further, as previous values have only higher expectations.
                    break;
                }

                // Weather for that day can be improved by this day.
                $results[$pendingBetterWeatherDay] = $day - $pendingBetterWeatherDay;
                $pendingBetterWeatherDays->pop();
            }

            $pendingBetterWeatherDays->push($day);
        }

        return $results;
    }
}
