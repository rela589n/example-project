<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250501134703 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD secret_key VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.secret_key IS '(DC2Type:secret_key)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP secret_key
        SQL);
    }
}
