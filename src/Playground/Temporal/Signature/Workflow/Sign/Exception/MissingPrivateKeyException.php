<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign\Exception;

use RuntimeException;

final class MissingPrivateKeyException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Missing private key for signing the document.');
    }
}
