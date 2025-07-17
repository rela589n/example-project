<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250715094216 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE blog_post_comments (id UUID NOT NULL, text TEXT NOT NULL, added_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, author_id UUID DEFAULT NULL, post_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_93F5D5E7F675F31B ON blog_post_comments (author_id)');
        $this->addSql('CREATE INDEX IDX_93F5D5E74B89032C ON blog_post_comments (post_id)');
        $this->addSql('ALTER TABLE blog_post_comments ADD CONSTRAINT FK_93F5D5E7F675F31B FOREIGN KEY (author_id) REFERENCES blog_users (id)');
        $this->addSql('ALTER TABLE blog_post_comments ADD CONSTRAINT FK_93F5D5E74B89032C FOREIGN KEY (post_id) REFERENCES blog_posts (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post_comments DROP CONSTRAINT FK_93F5D5E7F675F31B');
        $this->addSql('ALTER TABLE blog_post_comments DROP CONSTRAINT FK_93F5D5E74B89032C');
        $this->addSql('DROP TABLE blog_post_comments');
    }
}
