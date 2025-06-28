<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250627075320 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE accounting_accounts (
                id UUID NOT NULL,
                user_id UUID NOT NULL, 
                number INT NOT NULL,
                created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id, user_id)
            ) PARTITION BY LIST (user_id)
        SQL
        );
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE accounting_accounts_number_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DROP TABLE accounting_accounts
        SQL,
        );
        $this->addSql(<<<'SQL'
            DROP SEQUENCE accounting_accounts_number_seq
        SQL,
        );
    }
}
