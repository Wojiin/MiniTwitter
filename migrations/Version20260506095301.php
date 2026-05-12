<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506095301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP INDEX UNIQ_BF5476CA2B16A20F, ADD INDEX IDX_BF5476CA2B16A20F (reply_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX UNIQ_BF5476CAE18C365B, ADD INDEX IDX_BF5476CAE18C365B (post_notif_id)');
        $this->addSql('ALTER TABLE notification ADD discussion_notif_id INT DEFAULT NULL, CHANGE receive_id receive_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAEB392EED FOREIGN KEY (discussion_notif_id) REFERENCES discussion (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAEB392EED ON notification (discussion_notif_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CA2B16A20F, ADD UNIQUE INDEX UNIQ_BF5476CA2B16A20F (reply_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CAE18C365B, ADD UNIQUE INDEX UNIQ_BF5476CAE18C365B (post_notif_id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAEB392EED');
        $this->addSql('DROP INDEX IDX_BF5476CAEB392EED ON notification');
        $this->addSql('ALTER TABLE notification DROP discussion_notif_id, CHANGE receive_id receive_id INT DEFAULT NULL');
    }
}
