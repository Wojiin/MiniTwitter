<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260507100341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, INDEX IDX_4C62E638F624B39D (sender_id), INDEX IDX_4C62E638CD53EDB6 (receiver_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification DROP INDEX UNIQ_BF5476CA2B16A20F, ADD INDEX IDX_BF5476CA2B16A20F (reply_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX UNIQ_BF5476CA57477BEB, ADD INDEX IDX_BF5476CA57477BEB (message_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX UNIQ_BF5476CAE18C365B, ADD INDEX IDX_BF5476CAE18C365B (post_notif_id)');
        $this->addSql('ALTER TABLE notification ADD discussion_notif_id INT DEFAULT NULL, CHANGE receive_id receive_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAEB392EED FOREIGN KEY (discussion_notif_id) REFERENCES discussion (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAEB392EED ON notification (discussion_notif_id)');
        $this->addSql('ALTER TABLE post ADD id_citation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D48184740 FOREIGN KEY (id_citation_id) REFERENCES post (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D48184740 ON post (id_citation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638F624B39D');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638CD53EDB6');
        $this->addSql('DROP TABLE contact');
        $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CA2B16A20F, ADD UNIQUE INDEX UNIQ_BF5476CA2B16A20F (reply_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CA57477BEB, ADD UNIQUE INDEX UNIQ_BF5476CA57477BEB (message_notif_id)');
        $this->addSql('ALTER TABLE notification DROP INDEX IDX_BF5476CAE18C365B, ADD UNIQUE INDEX UNIQ_BF5476CAE18C365B (post_notif_id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAEB392EED');
        $this->addSql('DROP INDEX IDX_BF5476CAEB392EED ON notification');
        $this->addSql('ALTER TABLE notification DROP discussion_notif_id, CHANGE receive_id receive_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D48184740');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D48184740 ON post');
        $this->addSql('ALTER TABLE post DROP id_citation_id');
    }
}
