<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250630102636 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE accounting_account_transactions (
                id UUID NOT NULL,
                amount INT NOT NULL,
                description VARCHAR(255) NOT NULL,
                created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
                account_id UUID DEFAULT NULL,
                user_id UUID DEFAULT NULL,
                PRIMARY KEY(id, user_id)
            ) PARTITION BY LIST (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8A9C75AF9B6B5FBAA76ED395 ON accounting_account_transactions (account_id, user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accounting_account_transactions ADD CONSTRAINT FK_8A9C75AF9B6B5FBAA76ED395 FOREIGN KEY (account_id, user_id) REFERENCES accounting_accounts (id, user_id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE accounting_account_transactions DROP CONSTRAINT FK_8A9C75AF9B6B5FBAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE accounting_account_transactions
        SQL);
    }
}
