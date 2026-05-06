<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428144706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author VARCHAR(150) NOT NULL, title VARCHAR(150) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, count_repost INT NOT NULL, count_like INT NOT NULL, status VARCHAR(50) NOT NULL, count_flag INT NOT NULL, creator_id INT NOT NULL, INDEX IDX_5A8A6C8D61220EA6 (creator_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reply (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, status VARCHAR(50) NOT NULL, count_flag INT NOT NULL, creator_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_FDA8C6E061220EA6 (creator_id), INDEX IDX_FDA8C6E04B89032C (post_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, user_name VARCHAR(150) NOT NULL, status VARCHAR(50) NOT NULL, count_flag INT NOT NULL, delete_flag INT NOT NULL, last_login_at DATETIME NOT NULL, profil_picture VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_post_likes (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_79B6047EA76ED395 (user_id), INDEX IDX_79B6047E4B89032C (post_id), PRIMARY KEY (user_id, post_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_post_reposts (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_4DF90643A76ED395 (user_id), INDEX IDX_4DF906434B89032C (post_id), PRIMARY KEY (user_id, post_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D61220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E061220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E04B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_likes ADD CONSTRAINT FK_79B6047E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF90643A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_reposts ADD CONSTRAINT FK_4DF906434B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D61220EA6');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E061220EA6');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E04B89032C');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047EA76ED395');
        $this->addSql('ALTER TABLE user_post_likes DROP FOREIGN KEY FK_79B6047E4B89032C');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF90643A76ED395');
        $this->addSql('ALTER TABLE user_post_reposts DROP FOREIGN KEY FK_4DF906434B89032C');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE reply');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_post_likes');
        $this->addSql('DROP TABLE user_post_reposts');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
