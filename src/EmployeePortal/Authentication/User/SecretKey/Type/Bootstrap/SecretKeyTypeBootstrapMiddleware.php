<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey\Type\Bootstrap;

use App\EmployeePortal\Authentication\User\SecretKey\EncryptionService;
use App\EmployeePortal\Authentication\User\SecretKey\Type\SecretKeyType;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Types\Type;

final readonly class SecretKeyTypeBootstrapMiddleware implements Middleware
{
    public function __construct(
        private EncryptionService $encryptionService,
    ) {
    }

    public function wrap(Driver $driver): Driver
    {
        /** @var SecretKeyType $type */
        $type = Type::getType(SecretKeyType::NAME);

        $type->setEncryptor($this->encryptionService);

        return $driver;
    }
}
