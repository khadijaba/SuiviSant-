<?php
// create_forum_tables.php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config/bootstrap.php';

use App\Entity\Question;
use App\Entity\Answer;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

// Load the .env file if bootstrap.php doesn't do it
if (!isset($_ENV['DATABASE_URL'])) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/.env');
}

// Database configuration parameters
$isDevMode = true;
$proxyDir = null;
$cache = null;

// Parse the DATABASE_URL for connection parameters
$dbUrl = $_ENV['DATABASE_URL'] ?? '';
$dbParams = parse_url($dbUrl);
$dbParams = [
    'driver'   => 'pdo_mysql',
    'host'     => $dbParams['host'] ?? 'localhost',
    'user'     => $dbParams['user'] ?? 'root',
    'password' => $dbParams['pass'] ?? '',
    'dbname'   => ltrim($dbParams['path'] ?? '/symfony', '/'),
    'charset'  => 'utf8mb4'
];

try {
    // Create configuration
    $config = ORMSetup::createAttributeMetadataConfiguration(
        [__DIR__.'/src/Entity'],
        $isDevMode
    );

    // Get connection
    $connection = DriverManager::getConnection($dbParams, $config);
    
    // Create EntityManager
    $entityManager = new EntityManager($connection, $config);
    
    // Create Schema Tool
    $schemaTool = new SchemaTool($entityManager);
    
    // Get the metadata for our entities
    $classes = [
        $entityManager->getClassMetadata(Question::class),
        $entityManager->getClassMetadata(Answer::class)
    ];
    
    // Generate and display the SQL
    $sql = $schemaTool->getCreateSchemaSql($classes);
    echo "Generated SQL:\n";
    foreach ($sql as $query) {
        echo $query . ";\n";
    }
    
    // Create the tables
    echo "\nCreating tables...\n";
    $schemaTool->createSchema($classes);
    echo "Tables created successfully!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 