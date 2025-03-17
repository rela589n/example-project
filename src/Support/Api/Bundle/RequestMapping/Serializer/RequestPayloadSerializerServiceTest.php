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
        $requestPayloadSerializer = self::getContainer()->get(RequestPayloadSerializer::class);

        self::assertInstanceOf(RequestPayloadSerializer::class, $requestPayloadSerializer);
    }
}
