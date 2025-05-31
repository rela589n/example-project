<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign\Exception;

use RuntimeException;

final class BadCredentialsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Invalid credentials provided for signing the document. Please check your password and try again.'
        );
    }
}
