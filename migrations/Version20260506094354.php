<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506094354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion_user ADD CONSTRAINT FK_A8FD7A7F1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discussion_user ADD CONSTRAINT FK_A8FD7A7FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F13933E7B FOREIGN KEY (send_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA4CB96DCC FOREIGN KEY (receive_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2B16A20F FOREIGN KEY (reply_notif_id) REFERENCES reply (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA57477BEB FOREIGN KEY (message_notif_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE18C365B FOREIGN KEY (post_notif_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post CHANGE id_citation id_citation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D61220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D48184740 FOREIGN KEY (id_citation_id) REFERENCES post (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D48184740 ON post (id_citation_id)');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF04B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag ADD CONSTRAINT FK_5ACE3AF0BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E061220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF90643A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF906434B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion_user DROP FOREIGN KEY FK_A8FD7A7F1ADED311');
        $this->addSql('ALTER TABLE discussion_user DROP FOREIGN KEY FK_A8FD7A7FA76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F13933E7B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1ADED311');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA4CB96DCC');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2B16A20F');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA57477BEB');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE18C365B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D61220EA6');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC992FAEF');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D48184740');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D48184740 ON post');
        $this->addSql('ALTER TABLE post CHANGE id_citation_id id_citation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF04B89032C');
        $this->addSql('ALTER TABLE post_tag DROP FOREIGN KEY FK_5ACE3AF0BAD26311');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E061220EA6');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E04B89032C');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0C992FAEF');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C992FAEF');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047EA76ED395');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047E4B89032C');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF90643A76ED395');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF906434B89032C');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
    }
}
