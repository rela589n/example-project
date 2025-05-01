<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey\Type;

use App\EmployeePortal\Authentication\User\SecretKey\EncryptionService;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Override;
use SensitiveParameter;
use Webmozart\Assert\Assert;

final class SecretKeyType extends Type
{
    public const NAME = 'secret_key';

    private EncryptionService $encryptor;

    public function setEncryptor(EncryptionService $encryptor): void
    {
        $this->encryptor = $encryptor;
    }

    #[Override]
    public function convertToDatabaseValue(#[SensitiveParameter] $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        Assert::string($value);

        return $this->encryptor->encryptSecret($value);
    }

    #[Override]
    public function convertToPHPValue(#[SensitiveParameter] $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        Assert::string($value);

        return $this->encryptor->decryptSecret(rtrim($value));
    }

    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        // this is required to make doctrine migrations diff command
        // not to generate alter column statement every time
        return true;
    }
}
