<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218124116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dispo_expert CHANGE time time TIME NOT NULL, CHANGE location location VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD dispo_expert_id INT DEFAULT NULL, CHANGE message message VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous ADD CONSTRAINT FK_65E8AA0AB602E5C1 FOREIGN KEY (dispo_expert_id) REFERENCES dispo_expert (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65E8AA0AB602E5C1 ON rendez_vous (dispo_expert_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dispo_expert CHANGE time time TIME DEFAULT NULL, CHANGE location location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous DROP FOREIGN KEY FK_65E8AA0AB602E5C1');
        $this->addSql('DROP INDEX UNIQ_65E8AA0AB602E5C1 ON rendez_vous');
        $this->addSql('ALTER TABLE rendez_vous DROP dispo_expert_id, CHANGE message message VARCHAR(255) NOT NULL');
    }
}
