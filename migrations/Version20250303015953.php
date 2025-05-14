<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303015953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dispo_expert ADD expert_id INT NOT NULL');
        $this->addSql('ALTER TABLE dispo_expert ADD CONSTRAINT FK_B44ADD43C5568CE4 FOREIGN KEY (expert_id) REFERENCES expert (id)');
        $this->addSql('CREATE INDEX IDX_B44ADD43C5568CE4 ON dispo_expert (expert_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dispo_expert DROP FOREIGN KEY FK_B44ADD43C5568CE4');
        $this->addSql('DROP INDEX IDX_B44ADD43C5568CE4 ON dispo_expert');
        $this->addSql('ALTER TABLE dispo_expert DROP expert_id');
    }
}
