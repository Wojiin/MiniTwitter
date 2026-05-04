<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504082128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D61220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E061220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user CHANGE profil_picture profil_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF90643A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF906434B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D61220EA6');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC992FAEF');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E061220EA6');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E04B89032C');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0C992FAEF');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C992FAEF');
        $this->addSql('ALTER TABLE `user` CHANGE profil_picture profil_picture VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047EA76ED395');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047E4B89032C');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF90643A76ED395');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF906434B89032C');
    }
}
