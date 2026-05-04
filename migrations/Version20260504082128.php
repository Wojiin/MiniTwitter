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
        return 'Align profil_picture length with the User entity mapping.';
    }

    public function up(Schema $schema): void
    {
        // Earlier migrations already create all foreign keys. Re-adding them here
        // breaks clean installs with duplicate FK names.
        $this->addSql('ALTER TABLE user CHANGE profil_picture profil_picture VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` CHANGE profil_picture profil_picture VARCHAR(50) DEFAULT NULL');
    }
}
