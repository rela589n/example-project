<?php

declare(strict_types=1);

namespace App\Support\Api\Bundle\RequestMapping\Serializer;

use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversNothing]
final class RequestPayloadSerializerServiceTest extends KernelTestCase
{
    public function testSerializerServiceIsRegistered(): void
    {
        /** @var mixed $requestPayloadSerializer */
        $requestPayloadSerializer = self::getContainer()->get(RequestPayloadSerializer::class);

        self::assertSame(RequestPayloadSerializer::class, $requestPayloadSerializer::class);
    }
}
