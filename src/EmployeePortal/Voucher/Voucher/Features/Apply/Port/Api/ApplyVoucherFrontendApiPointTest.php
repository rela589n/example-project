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

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
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
                    'items' => [
                        [
                            'id' => 1,
                            'price' => 100,
                        ],
                        [
                            'id' => 1,
                            'price' => 100,
                        ],
                        [
                            'id' => 1,
                            'price' => 100,
                        ],
                    ],
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
                    'price_with_discount' => 66,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 67,
                ],
                [
                    'id' => 1,
                    'price' => 100,
                    'price_with_discount' => 67,
                ],
            ],
            'code' => '4KKKvRooJfif3jaGCbEMuE',
        ], $responseArray);
    }
}
