<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250627075237 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE auth_jwt_refresh_tokens ALTER id ADD GENERATED BY DEFAULT AS IDENTITY
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE auth_jwt_refresh_tokens ALTER id DROP IDENTITY
        SQL);
    }
}
