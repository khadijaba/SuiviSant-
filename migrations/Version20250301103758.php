<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301103758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport ADD titre VARCHAR(255) NOT NULL, ADD nom_patient VARCHAR(255) NOT NULL, ADD date_naissance DATE NOT NULL, ADD date_consultation DATE NOT NULL, ADD profession VARCHAR(255) DEFAULT NULL, ADD etat_civil VARCHAR(255) DEFAULT NULL, ADD motif_consultation LONGTEXT DEFAULT NULL, ADD antecedents_psychiatriques LONGTEXT DEFAULT NULL, ADD antecedents_familiaux LONGTEXT DEFAULT NULL, ADD traitements_anterieurs LONGTEXT DEFAULT NULL, ADD symptomes LONGTEXT DEFAULT NULL, ADD troubles_sommeil LONGTEXT DEFAULT NULL, ADD difficultes_concentration LONGTEXT DEFAULT NULL, ADD examen_clinique LONGTEXT DEFAULT NULL, ADD medicaments LONGTEXT DEFAULT NULL, ADD programme_propose LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport DROP titre, DROP nom_patient, DROP date_naissance, DROP date_consultation, DROP profession, DROP etat_civil, DROP motif_consultation, DROP antecedents_psychiatriques, DROP antecedents_familiaux, DROP traitements_anterieurs, DROP symptomes, DROP troubles_sommeil, DROP difficultes_concentration, DROP examen_clinique, DROP medicaments, DROP programme_propose');
    }
}
