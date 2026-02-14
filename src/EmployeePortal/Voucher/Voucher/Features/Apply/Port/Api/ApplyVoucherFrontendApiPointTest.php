<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\Features\Apply\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ApplyVoucherFrontendApiPoint::class)]
final class ApplyVoucherFrontendApiPointTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    public function testApplyVoucher(): void
    {
        $response = $this->client->request(
            'POST',
            '/api/example-project/voucher/apply',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'code' => '4KKKvRooJfif3jaGCbEMuE',
                    'items' => array_fill(0, 6, [
                        'id' => 1,
                        'price' => 100,
                    ]),
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);

        $responseArray = $response->toArray();
        self::assertSame([
            'items' => [
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 83,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 83,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 83,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 83,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 84,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 84,
                ],
            ],
            'code' => '4KKKvRooJfif3jaGCbEMuE',
        ], $responseArray);
    }
}
