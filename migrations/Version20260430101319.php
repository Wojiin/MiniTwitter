<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260430101319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD user_flag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DC992FAEF ON post (user_flag_id)');
        $this->addSql('ALTER TABLE reply ADD user_flag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E0C992FAEF ON reply (user_flag_id)');
        $this->addSql('ALTER TABLE user ADD user_flag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C992FAEF FOREIGN KEY (user_flag_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C992FAEF ON user (user_flag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC992FAEF');
        $this->addSql('DROP INDEX IDX_5A8A6C8DC992FAEF ON post');
        $this->addSql('ALTER TABLE post DROP user_flag_id');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0C992FAEF');
        $this->addSql('DROP INDEX IDX_FDA8C6E0C992FAEF ON reply');
        $this->addSql('ALTER TABLE reply DROP user_flag_id');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C992FAEF');
        $this->addSql('DROP INDEX IDX_8D93D649C992FAEF ON `user`');
        $this->addSql('ALTER TABLE `user` DROP user_flag_id');
    }
}
