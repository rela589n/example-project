<?php

declare(strict_types=1);

namespace App\EmployeePortal\Accounting\Account\Features\GetList\Port;

use App\EmployeePortal\Accounting\Account\Account;
use App\EmployeePortal\Accounting\Account\Features\GetList\AccountDto;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetAccountsListQuery
{
    public function execute(EntityManagerInterface $entityManager): array
    {
        $accounts = $entityManager->getRepository(Account::class)->findAll();

        return array_map(AccountDto::fromEntity(...), $accounts);
    }
}
