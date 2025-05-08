<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey\Type;

use App\EmployeePortal\Authentication\User\SecretKey\EncryptionService;
use App\EmployeePortal\Authentication\User\SecretKey\SecretKey;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Override;
use ReflectionClass;
use SensitiveParameter;
use Webmozart\Assert\Assert;

final class SecretKeyType extends Type
{
    public const NAME = 'secret_key';

    private EncryptionService $encryptor;

    private ReflectionClass $reflector;

    public function initialize(EncryptionService $encryptor): void
    {
        $this->encryptor = $encryptor;
        $this->reflector = new ReflectionClass(SecretKey::class);
    }

    #[Override]
    public function convertToDatabaseValue(#[SensitiveParameter] $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        Assert::isInstanceOf($value, SecretKey::class);

        return $this->encryptor->encrypt($value->getKey());
    }

    #[Override]
    public function convertToPHPValue(#[SensitiveParameter] $value, AbstractPlatform $platform): ?SecretKey
    {
        if (null === $value) {
            return null;
        }

        Assert::string($value);

        /** @var SecretKey $secretKeyGhost */
        $secretKeyGhost = $this->reflector->newLazyProxy(fn (): SecretKey => $this->encryptor->decrypt($value));

        return $secretKeyGhost;
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
