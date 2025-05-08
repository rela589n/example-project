<?php

declare(strict_types=1);

namespace App\EmployeePortal\Authentication\User\SecretKey;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EncryptionService::class)]
final class EncryptionServiceUnitTest extends TestCase
{
    private EncryptionService $encryptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $encryptionKey = random_bytes(32);

        $this->encryptionService = new EncryptionService($encryptionKey);
    }

    public function testEncryptionAndDecryptionWorksProperly(): void
    {
        $originalKey = new SecretKey('foo bar baz');

        $encryptedString = $this->encryptionService->encrypt($originalKey);

        $secretKey = $this->encryptionService->decrypt($encryptedString);

        self::assertTrue($secretKey->equals($originalKey));
    }
}
