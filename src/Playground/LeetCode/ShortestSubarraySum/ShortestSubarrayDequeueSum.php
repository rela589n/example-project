<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\ShortestSubarraySum;

use SplDoublyLinkedList;

final readonly class ShortestSubarrayDequeueSum implements ShortestSubarraySum
{
    /** @param list<int> $nums */
    public function shortestSubarray(array $nums, int $target): int
    {
        if ($target <= 0) {
            return 0;
        }

        $sum = 0;
        /** @var SplDoublyLinkedList<array{int,int}> $prefixSumQueue */
        $prefixSumQueue = new SplDoublyLinkedList();
        $prefixSumQueue->push([0, -1]); // not taking any item would result in zero-sum
        $minLength = PHP_INT_MAX;

        foreach ($nums as $r => $num) {
            $sum += $num;

            // We need to find the minimum item that we can subtract from the sum
            // to find if there's "better" sum that satisfies the desired target.
            //
            // since prefixSumQueue is monotonically increasing, we try to get these one by one

            while (!$prefixSumQueue->isEmpty()) {
                [$prefixSum, $endIndex] = $prefixSumQueue->bottom();

                if ($sum - $prefixSum < $target) {
                    break;
                }

                $minLength = min($minLength, $r - $endIndex);
                $prefixSumQueue->shift();
            }

            // before insertion, verify that dequeue is monotonically increasing
            // we don't care about items that are higher than the current sum, since
            // if the current sum is less than those, it'd be the one that is better to be subtracted
            while (!$prefixSumQueue->isEmpty()) {
                [$lastItem] = $prefixSumQueue->top();

                if ($lastItem < $sum) {
                    break;
                }

                $prefixSumQueue->pop();
            }

            $prefixSumQueue->push([$sum, $r]);
        }

        if ($minLength === PHP_INT_MAX) {
            return -1;
        }

        return $minLength;
    }
}
