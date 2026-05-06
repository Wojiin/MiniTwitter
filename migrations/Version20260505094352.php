<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505094352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Earlier migrations already create the existing post/reply/user FK constraints.
        // Re-adding them here causes duplicate FK name errors on databases that are
        // already up to date. This migration only needs to create the new tag tables.
        if (!$schema->hasTable('tag')) {
            $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        }

        if (!$schema->hasTable('tag_post')) {
            $this->addSql('CREATE TABLE tag_post (tag_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_B485D33BBAD26311 (tag_id), INDEX IDX_B485D33B4B89032C (post_id), PRIMARY KEY (tag_id, post_id)) DEFAULT CHARACTER SET utf8mb4');
            $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT FK_B485D33BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT FK_B485D33B4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // Only revert the tables created in this migration.
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY FK_B485D33BBAD26311');
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY FK_B485D33B4B89032C');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_post');
    }
}
