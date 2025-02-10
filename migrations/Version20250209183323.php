<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250209183323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ressource_educative ADD categorie_id INT NOT NULL, DROP categorie, CHANGE video video VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ressource_educative ADD CONSTRAINT FK_B190A431BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_ressource (id)');
        $this->addSql('CREATE INDEX IDX_B190A431BCF5E72D ON ressource_educative (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ressource_educative DROP FOREIGN KEY FK_B190A431BCF5E72D');
        $this->addSql('DROP INDEX IDX_B190A431BCF5E72D ON ressource_educative');
        $this->addSql('ALTER TABLE ressource_educative ADD categorie VARCHAR(255) NOT NULL, DROP categorie_id, CHANGE video video VARCHAR(255) NOT NULL');
    }
}
