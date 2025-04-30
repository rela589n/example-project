<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250430100848 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user_registered_events ALTER password_hash SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX IDX_BEA9ACFE7CBBE7 RENAME TO IDX_C99CDE10FE7CBBE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER password_hash SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user_registered_events ALTER password_hash DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER INDEX IDX_C99CDE10FE7CBBE7 RENAME TO IDX_BEA9ACFE7CBBE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ALTER password_hash DROP NOT NULL
        SQL);
    }
}
