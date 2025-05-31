<?php

declare(strict_types=1);

namespace App\Playground\Temporal\Signature\Workflow\Sign;

use App\Playground\Temporal\Signature\Workflow\Sign\Exception\BadCredentialsException;
use App\Playground\Temporal\Signature\Workflow\Sign\Exception\ExpiredPrivateKeyCertificateException;
use App\Playground\Temporal\Signature\Workflow\Sign\Exception\ExpiredPrivateKeyCertificateExceptionFormatter;
use App\Playground\Temporal\Signature\Workflow\Sign\Exception\MissingPrivateKeyException;
use Carbon\CarbonImmutable;
use PhPhD\ExceptionalValidation;

#[ExceptionalValidation]
final readonly class SignDocumentCommand
{
    public function __construct(
        #[ExceptionalValidation\Capture(MissingPrivateKeyException::class)]
        #[ExceptionalValidation\Capture(ExpiredPrivateKeyCertificateException::class, formatter: ExpiredPrivateKeyCertificateExceptionFormatter::class)]
        public string $documentId,
        #[ExceptionalValidation\Capture(BadCredentialsException::class)]
        public string $password,
    ) {
    }

    public function process(): string
    {
        if ($this->documentId === 'd00') {
            throw new MissingPrivateKeyException();
        }

        if ($this->documentId === 'd01') {
            throw new ExpiredPrivateKeyCertificateException(new CarbonImmutable('2022-01-01 12:00:00'));
        }

        if ($this->password === 'p00') {
            throw new BadCredentialsException();
        }

        return '/path/to/signed-file';
    }
}
