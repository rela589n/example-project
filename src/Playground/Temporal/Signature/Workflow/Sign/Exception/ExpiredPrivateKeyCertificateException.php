<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign\Exception;

use Carbon\CarbonImmutable;
use RuntimeException;

final class ExpiredPrivateKeyCertificateException extends RuntimeException
{
    public function __construct(
        private(set) readonly CarbonImmutable $expirationDate,
    ) {
        parent::__construct('The private key certificate has expired. Please renew your certificate to continue signing documents.');
    }
}
