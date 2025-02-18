<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211091138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C62FF6CDF');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09C62FF6CDF');
        $this->addSql('ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09C62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
    }
}
