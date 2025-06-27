<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250627074517 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events.user_id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events.timestamp IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_logged_in_events.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_request_created_event.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_request_created_event.password_reset_request_id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.user_id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.created_at IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.expires_at IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_registered_events.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_reset_password_events.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_reset_password_events.password_reset_request_id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.id IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.created_at IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.updated_at IS ''
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.secret_key IS ''
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.secret_key IS '(DC2Type:secret_key)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events."timestamp" IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_events.user_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_registered_events.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_logged_in_events.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.expires_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_requests.user_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_request_created_event.password_reset_request_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_password_reset_request_created_event.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_reset_password_events.password_reset_request_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_reset_password_events.id IS '(DC2Type:uuid)'
        SQL);
    }
}
