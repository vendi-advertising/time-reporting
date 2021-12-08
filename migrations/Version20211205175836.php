<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205175836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE time_entry (id INT NOT NULL, user_id INT NOT NULL, client_id INT NOT NULL, project_id INT NOT NULL, entry_date_time DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', hours NUMERIC(10, 2) NOT NULL, hours_rounded NUMERIC(10, 2) NOT NULL, notes VARCHAR(255) DEFAULT NULL, is_billed TINYINT(1) NOT NULL, is_closed TINYINT(1) NOT NULL, is_running TINYINT(1) NOT NULL, is_billable TINYINT(1) NOT NULL, is_budgeted TINYINT(1) NOT NULL, INDEX IDX_6E537C0CA76ED395 (user_id), INDEX IDX_6E537C0C19EB6921 (client_id), INDEX IDX_6E537C0C166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE time_entry ADD CONSTRAINT FK_6E537C0C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE time_entry');
    }
}
