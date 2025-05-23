<?php

declare(strict_types=1);

namespace App\Playground\LeetCode\ShortestSubarraySum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShortestSubarrayHeapSum::class)]
final class ShortestSubarraySumUnitTest extends TestCase
{
    private ShortestSubarraySum $algorithm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->algorithm = new ShortestSubarrayDequeueSum();
    }

    public function testReturnsMinusOneWhenArrayIsEmpty(): void
    {
        $result = $this->algorithm->shortestSubarray([], 1);

        self::assertSame(-1, $result);
    }

    public function testReturnsMinusOneWhenSumCanNotBeReached(): void
    {
        $result = $this->algorithm->shortestSubarray([1], 10);

        self::assertSame(-1, $result);
    }

    public function testReturns0WhenTargetIsZeroOrNegative(): void
    {
        $result = $this->algorithm->shortestSubarray([1], -10);

        self::assertSame(0, $result);
    }

    public function testSumConsistsOfTheWholeArray(): void
    {
        $result = $this->algorithm->shortestSubarray([1, 2, 3], 6);

        self::assertSame(3, $result);
    }

    public function testTargetIsReachedFromOneItem(): void
    {
        $result = $this->algorithm->shortestSubarray([1, 2, 99], 6);

        // items: [1, 2, 99]
        // sum:   [1, 3, 102]

        self::assertSame(1, $result);
    }

    public function testMoreOptimalSumCanBeFoundAfterLessOptimalSum(): void
    {
        $result = $this->algorithm->shortestSubarray([4, 1, 1, 1, 4, 3], 7);

        self::assertSame(2, $result);
    }

    public function testIfSumReachesNegativeValueItIsReset(): void
    {
        // if sum becomes negative, no point in keeping it (reset it)

        $result = $this->algorithm->shortestSubarray([2, -1, -2, 3, 0], 3);

        // items:  [2, -1, -2, 3, 0]
        // prefix: [2,  1, -1, 2, 2]
        // sum:    [_,  _,  _, 3, 3] (skip negative)

        self::assertSame(1, $result);
    }

    public function testNegativeNumberCanBePartOfTheOptimalSum(): void
    {
        // Weight of the prefix is it's value

        // If during L pointer shift, some of the further prefix sum isn't positive,
        // we should shift it to such element that is positive (or completely cut off the prefix).

        // items:  [ 1,  3, -3, 11,  -1,  2 ]
        // prefix: [ 1,  4,  1, 12,| 11, 13 ]
        //           ^ drop it (the least value)
        // prefix: [ _,  3,  0, 11, 10, 12 ]
        //                   ^ drop it (the least value)
        // prefix: [ _,  _,  _, 11, 10, 12 ]
        //                     (optimal sum)
        // Thus, cut off such prefix that makes up least value

        $result = $this->algorithm->shortestSubarray([1, 3, -3, 11, -1, 2], 12);

        self::assertSame(3, $result);
    }

    public function testNegativePartMustBeSubtractedAfterReachingTheTarget(): void
    {
        $result = $this->algorithm->shortestSubarray([2, 3, -4, 6, 1], 7);

        self::assertSame(2, $result);
    }

    public function testLeftPointerMovesForwardUntilMinimalItemsTargetIsReached(): void
    {
        $result = $this->algorithm->shortestSubarray([1, 2, 1, -2, 2, 3], 5);

        self::assertSame(2, $result);

        // items:    [1, 2, 1, -2, 2, 3]
        // heap:     [1, 3, 4,  2, 4, 7], sum: 7

        // heap(1):  [_, 3, 4,  2, 4, 7],
        // sum(1):   [_, 2, 3,  1, 3, 6], sum: 6

        // -heap(2): [_, _, 1, -1, 1, 4] (4 < 5), and prefix is positive: (1 + -1 + 1), but previous prefix would've been negative (1 - 1)
        // therefore, prefix sum contains the negative item, which would've been better without

        // heap(2):  [_, 3, 4,  _, 4, 7],
        // sum(2):   [_, _, _,  _, 2, 5], sum: 5
    }

    /**
     * After shifting left pointer, next sum values will not correspond to the existing prefix values in the heap.
     * This is because the first part was cut off, and the next sum values might be less than existing in the heap.
     *
     * It could be that the optimal solution is right after the second shift, but
     * since new inserted value sum is less than the existing one, next time pointer might shift too far.
     *
     * Therefore, heap must include shift to take into account that cut off.
     */
    public function testHeapShiftIsTakenIntoAccount(): void
    {
        // last two items must add up to 8
        $result = $this->algorithm->shortestSubarray([5, 1, 1, 1, 2, 6], 8);

        // target: 8
        // items:  5, 1, 1, 1,  2,  6
        //         L        R
        // sum:    5, 6, 7, 8,| 10, 16
        //            L             R
        // sum:    _, 1, 2, 3,  5,  11
        // heap:   _, 6, 7, 8 |(5), 11 - if no shift, 5 is selected for cut, and this is incorrect

        self::assertSame(2, $result);
    }

    public function testBigArray(): void
    {
        /** @var list<int> $array */
        $array = include __DIR__.'/data.php';
        $target = 396893380;

        $result = $this->algorithm->shortestSubarray($array, $target);

        self::assertSame(79517, $result);
    }
}
