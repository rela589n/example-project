<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey;

use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Webmozart\Assert\Assert;

use function base64_decode;
use function base64_encode;
use function openssl_decrypt;
use function openssl_encrypt;
use function random_bytes;
use function substr;

final readonly class EncryptionService
{
    private const int AES_BLOCK_SIZE = 16;

    public function __construct(
        /** Encryption key, it must be 256 bits (32 bytes) long */
        #[Autowire('%env(base64:ENCRYPTION_KEY)%')]
        private string $encryptionKey,
    ) {
    }

    public function encrypt(SecretKey $secretKey): string
    {
        $initializationVector = random_bytes(self::AES_BLOCK_SIZE);

        $encryptedValue = openssl_encrypt(
            $secretKey->getKey(),
            'AES-256-CBC',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $initializationVector,
        );

        Assert::notFalse($encryptedValue, 'Encryption failed');

        return base64_encode($initializationVector.$encryptedValue);
    }

    public function decrypt(#[SensitiveParameter] string $value): SecretKey
    {
        $binaryValue = base64_decode($value, true);

        $initializationVector = substr($binaryValue, 0, self::AES_BLOCK_SIZE);
        $encryptedValue = substr($binaryValue, self::AES_BLOCK_SIZE);

        $decryptedValue = openssl_decrypt(
            $encryptedValue,
            'AES-256-CBC',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $initializationVector,
        );

        Assert::notFalse($decryptedValue, 'Decryption failed');

        return new SecretKey($decryptedValue);
    }
}
