<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\Jwt\Tests\Constraint;

use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LogicalNot;

final class ValidJwtTokenPair extends Constraint
{
    public function toString(): string
    {
        return 'is valid jwt token pair';
    }

    protected function matches(mixed $other): bool
    {
        /** @var array{token: mixed, refreshToken: mixed} $other */

        return (new ArrayHasKey('token'))->matches($other)
            && (new IsType(IsType::TYPE_STRING))->matches($other['token'])
            && (new LogicalNot(new IsEmpty()))->matches($other['token'])
            && (new ArrayHasKey('refreshToken'))->matches($other)
            && (new IsType(IsType::TYPE_STRING))->matches($other['refreshToken'])
            && (new LogicalNot(new IsEmpty()))->matches($other['refreshToken']);
    }
}
