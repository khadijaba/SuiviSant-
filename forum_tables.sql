-- Create Question table
CREATE TABLE question (
    id INT AUTO_INCREMENT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) DEFAULT NULL,
    views INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    category VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Create Answer table
CREATE TABLE answer (
    id INT AUTO_INCREMENT NOT NULL,
    question_id INT NOT NULL,
    content LONGTEXT NOT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) DEFAULT NULL,
    is_accepted TINYINT(1) NOT NULL,
    votes INT NOT NULL,
    INDEX IDX_DADD4A251E27F6BF (question_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Add foreign key constraint
ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id); 