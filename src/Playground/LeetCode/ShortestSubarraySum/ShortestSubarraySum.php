<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\ShortestSubarraySum;

/** @see https://leetcode.com/problems/shortest-subarray-with-sum-at-least-k/description/ */
interface ShortestSubarraySum
{
    /** @param list<int> $nums */
    public function shortestSubarray(array $nums, int $target): int;
}
