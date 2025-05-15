<?php
// migration.php

// Get database credentials from .env file
$envFile = file_get_contents(__DIR__ . '/.env');
preg_match('/DATABASE_URL="mysql:\/\/([^:@]+)(?::([^@]*))?@([^:]+):(\d+)\/([^?"]+)/', $envFile, $matches);

if (count($matches) < 6) {
    echo "Failed to parse database credentials from .env file.\n";
    print_r($matches);
    exit(1);
}

$dbUser = $matches[1];
$dbPass = $matches[2] ?? ''; // Password may be empty
$dbHost = $matches[3];
$dbPort = $matches[4];
$dbName = $matches[5];

echo "Database connection details:\n";
echo "Host: $dbHost\n";
echo "Port: $dbPort\n";
echo "Database: $dbName\n";
echo "User: $dbUser\n";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Define SQL queries for creating forum tables
    $sql = [
        // Create Question table
        "CREATE TABLE IF NOT EXISTS question (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            author_name VARCHAR(255) NOT NULL,
            author_email VARCHAR(255) DEFAULT NULL,
            views INT NOT NULL,
            status VARCHAR(50) NOT NULL,
            category VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",
        
        // Create Answer table
        "CREATE TABLE IF NOT EXISTS answer (
            id INT AUTO_INCREMENT NOT NULL,
            question_id INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            author_name VARCHAR(255) NOT NULL,
            author_email VARCHAR(255) DEFAULT NULL,
            is_accepted TINYINT(1) NOT NULL,
            votes INT NOT NULL,
            INDEX IDX_DADD4A251E27F6BF (question_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB",
        
        // Add foreign key constraint
        "ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)"
    ];
    
    // Execute each SQL query
    foreach ($sql as $query) {
        echo "Executing: " . substr($query, 0, 50) . "...\n";
        $pdo->exec($query);
    }
    
    echo "Migration completed successfully! Forum tables have been created.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 