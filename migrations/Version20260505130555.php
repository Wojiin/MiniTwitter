<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505130555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, content VARCHAR(255) NOT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, receive_id INT DEFAULT NULL, reply_notif_id INT DEFAULT NULL, message_notif_id INT DEFAULT NULL, post_notif_id INT DEFAULT NULL, INDEX IDX_BF5476CA4CB96DCC (receive_id), UNIQUE INDEX UNIQ_BF5476CA2B16A20F (reply_notif_id), UNIQUE INDEX UNIQ_BF5476CA57477BEB (message_notif_id), UNIQUE INDEX UNIQ_BF5476CAE18C365B (post_notif_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_5ACE3AF04B89032C (post_id), INDEX IDX_5ACE3AF0BAD26311 (tag_id), PRIMARY KEY (post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4CB96DCC FOREIGN KEY (receive_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2B16A20F FOREIGN KEY (reply_notif_id) REFERENCES reply (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA57477BEB FOREIGN KEY (message_notif_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE18C365B FOREIGN KEY (post_notif_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY `FK_B485D33B4B89032C`');
        $this->addSql('ALTER TABLE tag_post DROP FOREIGN KEY `FK_B485D33BBAD26311`');
        $this->addSql('DROP TABLE tag_post');
        $this->addSql('ALTER TABLE user CHANGE count_notification count_notification INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_post (tag_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_B485D33BBAD26311 (tag_id), INDEX IDX_B485D33B4B89032C (post_id), PRIMARY KEY (tag_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT `FK_B485D33B4B89032C` FOREIGN KEY (post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_post ADD CONSTRAINT `FK_B485D33BBAD26311` FOREIGN KEY (tag_id) REFERENCES tag (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4CB96DCC');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2B16A20F');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA57477BEB');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE18C365B');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('ALTER TABLE `user` CHANGE count_notification count_notification INT NOT NULL');
    }
}
