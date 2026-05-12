<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505123758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // This migration duplicates relation work that is already handled by later,
        // corrected migrations. Keep it as a no-op to avoid re-creating/dropping
        // join tables and foreign keys unnecessarily.
    }

    public function down(Schema $schema): void
    {
        // No-op.
    }
}
