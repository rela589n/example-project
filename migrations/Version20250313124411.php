<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250313124411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE user_events (id UUID NOT NULL, user_id UUID NOT NULL, timestamp TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_36D54C77A76ED395 ON user_events (user_id)');
        $this->addSql('ALTER TABLE user_events ADD CONSTRAINT FK_36D54C77A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_events.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_events.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_events.timestamp IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE user_registered_events (id UUID NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE user_registered_events ADD CONSTRAINT FK_BF14BE20BF396750 FOREIGN KEY (id) REFERENCES user_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_registered_events.id IS \'(DC2Type:uuid)\'');

        $this->addSql('CREATE TABLE user_logged_in_events (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE user_logged_in_events ADD CONSTRAINT FK_9971E1F0BF396750 FOREIGN KEY (id) REFERENCES user_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_logged_in_events.id IS \'(DC2Type:uuid)\'');

        $this->addSql('CREATE TABLE user_password_reset_requests (id UUID NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A40E19A76ED395 ON user_password_reset_requests (user_id)');
        $this->addSql('ALTER TABLE user_password_reset_requests ADD CONSTRAINT FK_29A40E19A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_password_reset_requests.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_password_reset_requests.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_password_reset_requests.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_password_reset_requests.expires_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE user_password_reset_request_created_event (id UUID NOT NULL, password_reset_request_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EEADDE2CFE7CBBE7 ON user_password_reset_request_created_event (password_reset_request_id)');
        $this->addSql('ALTER TABLE user_password_reset_request_created_event ADD CONSTRAINT FK_EEADDE2CFE7CBBE7 FOREIGN KEY (password_reset_request_id) REFERENCES user_password_reset_requests (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_password_reset_request_created_event ADD CONSTRAINT FK_EEADDE2CBF396750 FOREIGN KEY (id) REFERENCES user_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_password_reset_request_created_event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_password_reset_request_created_event.password_reset_request_id IS \'(DC2Type:uuid)\'');

        $this->addSql('CREATE TABLE user_password_reset_events (id UUID NOT NULL, password_reset_request_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BEA9ACFE7CBBE7 ON user_password_reset_events (password_reset_request_id)');
        $this->addSql('ALTER TABLE user_password_reset_events ADD CONSTRAINT FK_BEA9ACBF396750 FOREIGN KEY (id) REFERENCES user_events (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_password_reset_events ADD CONSTRAINT FK_BEA9ACFE7CBBE7 FOREIGN KEY (password_reset_request_id) REFERENCES user_password_reset_requests (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN user_password_reset_events.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_password_reset_events.password_reset_request_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_events DROP CONSTRAINT FK_36D54C77A76ED395');
        $this->addSql('ALTER TABLE user_logged_in_events DROP CONSTRAINT FK_9971E1F0BF396750');
        $this->addSql('ALTER TABLE user_password_reset_events DROP CONSTRAINT FK_BEA9ACFE7CBBE7');
        $this->addSql('ALTER TABLE user_password_reset_events DROP CONSTRAINT FK_BEA9ACBF396750');
        $this->addSql('ALTER TABLE user_password_reset_request_created_event DROP CONSTRAINT FK_EEADDE2CFE7CBBE7');
        $this->addSql('ALTER TABLE user_password_reset_request_created_event DROP CONSTRAINT FK_EEADDE2CBF396750');
        $this->addSql('ALTER TABLE user_password_reset_requests DROP CONSTRAINT FK_29A40E19A76ED395');
        $this->addSql('ALTER TABLE user_registered_events DROP CONSTRAINT FK_BF14BE20BF396750');
        $this->addSql('DROP TABLE user_events');
        $this->addSql('DROP TABLE user_logged_in_events');
        $this->addSql('DROP TABLE user_password_reset_events');
        $this->addSql('DROP TABLE user_password_reset_request_created_event');
        $this->addSql('DROP TABLE user_password_reset_requests');
        $this->addSql('DROP TABLE user_registered_events');
        $this->addSql('DROP TABLE users');
    }
}
