<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\DailyTemperatures;

use SplDoublyLinkedList;

/** @see https://leetcode.com/problems/daily-temperatures/description/ */
final readonly class DailyTemperaturesSolution
{
    /**
     * @param list<int> $temperatures
     *
     * @return list<int>
     */
    public function dailyTemperatures(array $temperatures): array
    {
        // [73,74,75,71,69,72,76,73]
        //  73,74
        //     74,75
        //        75, _, _, _,76
        //           71, _ 72,
        //              69,72
        //                 72,76
        //                    76,_

        // What if we traverse from end to start to keep track
        // of where each particular temperature could be the result of?

        // [73,74,75,71,69,72,76,73]
        //                      [73]
        //                    76 discard [73], as it's less than 76, result = -1
        //                       queue: [76]
        //                 72 - result = 76, as it's first greater
        //                      queue: [72,76]
        //              69   - result = 72, as it's first that's greater
        //                      queue: [69, 72, 76]
        //           71      - result: 72; discard 69, as it won't participate in the equation
        //                     queue: [71, 72, 76]
        //        75         - result: 76, discard the rest, they won't participate in equation
        //                     queue: [75, 76]
        //     74            - result: 75
        //                     queue: [74, 75, 76]
        //  73               - result: 74 (first greater)
        //                     queue: [73]

        $stack = new SplDoublyLinkedList();

        $results = [];

        foreach (array_reverse($temperatures, true) as $day => $temperature) {
            while (!$stack->isEmpty()) {
                [$nextGreaterTemperature, $nextGreaterTemperatureDay] = $stack->bottom();

                if ($temperature < $nextGreaterTemperature) {
                    break;
                }

                $stack->shift();
            }

            if (!$stack->isEmpty()) {
                /** @noinspection PhpUndefinedVariableInspection */
                $results[] = $nextGreaterTemperatureDay - $day;
            } else {
                // no solution for current temperature

                $results[] = 0;
            }

            $stack->unshift([$temperature, $day]);
        }

        return array_reverse($results);
    }
}
