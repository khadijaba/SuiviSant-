<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Forum tables creation
 */
final class Version20250601120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Question and Answer tables for the forum system';
    }

    public function up(Schema $schema): void
    {
        // Create Question table
        $this->addSql('CREATE TABLE IF NOT EXISTS question (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            author_name VARCHAR(255) NOT NULL,
            author_email VARCHAR(255) DEFAULT NULL,
            views INT NOT NULL,
            status VARCHAR(50) NOT NULL,
            category VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create Answer table
        $this->addSql('CREATE TABLE IF NOT EXISTS answer (
            id INT AUTO_INCREMENT NOT NULL,
            question_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            author_name VARCHAR(255) NOT NULL,
            author_email VARCHAR(255) DEFAULT NULL,
            is_accepted TINYINT(1) NOT NULL,
            votes INT NOT NULL,
            INDEX IDX_DADD4A251E27F6BF (question_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop tables in reverse order
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE question');
    }
} 