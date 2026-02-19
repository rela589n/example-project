<?php

declare(strict_types=1);

namespace App\EmployeePortal\Voucher\Voucher\_Features\Create\Port\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\EmployeePortal\Voucher\Voucher\Voucher;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CreateVoucherFrontendApiPoint::class)]
final class CreateVoucherFrontendApiPointTest extends ApiTestCase
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

    public function testCreateVoucher(): void
    {
        $response = $this->client->request(
            'POST',
            '/api/example-project/voucher/generate',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
                'json' => [
                    'discount' => 123,
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);

        /** @var array{code: string} $responseArray */
        $responseArray = $response->toArray();

        $id = Uuid::fromBase58($responseArray['code']);

        $voucher = $this->entityManager->getRepository(Voucher::class)->findOneBy(['id' => $id]);

        self::assertNotNull($voucher, 'Voucher should be created');
        self::assertSame(123, $voucher->discount);
        self::assertLessThanOrEqual(10, CarbonImmutable::now()->diffInSeconds($voucher->createdAt));
    }
}
