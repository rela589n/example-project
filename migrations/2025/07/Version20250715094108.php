<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250715094108 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE blog_posts (id UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, author_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_78B2F932F675F31B ON blog_posts (author_id)');
        $this->addSql('CREATE INDEX IDX_78B2F9327E3C61F9 ON blog_posts (owner_id)');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F932F675F31B FOREIGN KEY (author_id) REFERENCES blog_users (id)');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F9327E3C61F9 FOREIGN KEY (owner_id) REFERENCES blog_users (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_posts DROP CONSTRAINT FK_78B2F932F675F31B');
        $this->addSql('ALTER TABLE blog_posts DROP CONSTRAINT FK_78B2F9327E3C61F9');
        $this->addSql('DROP TABLE blog_posts');
    }
}
