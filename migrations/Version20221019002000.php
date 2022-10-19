<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019002000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE favorite_projects favorite_projects LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE favorite_clients favorite_clients LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE favorite_projects favorite_projects LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE favorite_clients favorite_clients LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
