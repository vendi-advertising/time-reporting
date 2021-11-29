<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129124411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project CHANGE budget_total budget_total NUMERIC(10, 2) DEFAULT NULL, CHANGE budget_spent budget_spent NUMERIC(10, 2) DEFAULT NULL, CHANGE budget_remaining budget_remaining NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project CHANGE budget_total budget_total DOUBLE PRECISION DEFAULT NULL, CHANGE budget_spent budget_spent DOUBLE PRECISION DEFAULT NULL, CHANGE budget_remaining budget_remaining DOUBLE PRECISION DEFAULT NULL');
    }
}
