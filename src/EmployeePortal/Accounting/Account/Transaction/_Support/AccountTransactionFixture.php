<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Transaction\_Support;

use App\EmployeePortal\Accounting\Account\_Support\AccountFixture;
use App\EmployeePortal\Accounting\Account\Account;
use App\EmployeePortal\Accounting\Account\Transaction\AccountTransaction;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\Uid\Uuid;

use function sprintf;

final class AccountTransactionFixture extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            AccountFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        Clock::set(new MockClock(new DateTimeImmutable('2023-01-01T12:00:00+00:00')));

        $transactions = [
            // User 1, Account 1
            [
                'id' => '4fb30e63-53a3-7a20-a2be-074910848bef',
                'account_id' => 'a3a8c8c9-2336-753b-b970-51d2540a40ec',
                'user_id' => '2a977708-1c69-7d38-9074-b388a7f386dc',
                'amount' => 10000,
                'description' => 'Initial deposit',
            ],
            // User 1, Account 2
            [
                'id' => '3a1675f7-0fac-7ac0-9434-30d0a21b8956',
                'account_id' => '1bb783a8-0813-7a43-801a-5c0e90ad9841',
                'user_id' => '2a977708-1c69-7d38-9074-b388a7f386dc',
                'amount' => 5000,
                'description' => 'Initial deposit',
            ],
            // User 2, Account 1
            [
                'id' => '10ba1a8f-65aa-799f-80f4-b3f26558a5db',
                'account_id' => '268cef29-798b-7512-9560-a6dec7af72fd',
                'user_id' => 'de13a4f3-b43e-74d4-aca9-7ce087a21b73',
                'amount' => 7500,
                'description' => 'Initial deposit',
            ],
        ];

        foreach ($transactions as $transactionData) {
            $account = $manager->getRepository(Account::class)->find([
                'id.id' => Uuid::fromString($transactionData['account_id']),
                'id.userId' => Uuid::fromString($transactionData['user_id']),
            ]);

            if (!$account) {
                throw new RuntimeException(sprintf(
                    'Account with ID %s and User ID %s not found.',
                    $transactionData['account_id'],
                    $transactionData['user_id'],
                ));
            }

            $transaction = new AccountTransaction(
                Uuid::fromString($transactionData['id']),
                $account,
                $transactionData['amount'],
                $transactionData['description'],
                new CarbonImmutable('2023-01-01T12:00:00+00:00'),
            );

            $manager->persist($transaction);
        }

        $manager->flush();

        Clock::set(new NativeClock());
    }
}
