<?php
// Edit programme script
require_once dirname(__DIR__).'/vendor/autoload.php';

// Database connection parameters
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'pideva';
$dbPort = 3306;

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!isset($_GET['id'])) {
        header("Location: programme_admin.php");
        exit;
    }
    
    $id = $_GET['id'];
    
    // Process form submission for editing a programme
    if (isset($_POST['edit_programme'])) {
        $nom = $_POST['nom'];
        $date = $_POST['date'];
        $progression = $_POST['progression'];
        $evaluation = isset($_POST['evaluation']) ? $_POST['evaluation'] : 0;
        
        // Update in database
        $stmt = $pdo->prepare("UPDATE programme SET nom = ?, date = ?, progression = ?, evaluation = ? WHERE id = ?");
        $stmt->execute([$nom, $date, $progression, $evaluation, $id]);
        
        // Redirect to the admin page
        header("Location: programme_admin.php?updated=1");
        exit;
    }
    
    // Get the programme to edit
    $stmt = $pdo->prepare("SELECT * FROM programme WHERE id = ?");
    $stmt->execute([$id]);
    $programme = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$programme) {
        header("Location: programme_admin.php");
        exit;
    }
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Programme</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            color: #ecf0f1;
            padding: 1rem;
            position: fixed;
            width: 240px;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1rem;
            padding: 0.8rem;
            display: block;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .sidebar .sidebar-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }
        .main-content {
            margin-left: 260px;
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">Admin Dashboard</div>
    <a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <a href="/dispoexpert"><i class="fas fa-users"></i> Disponibilité Expert</a>
    <a href="/rendezvous"><i class="fas fa-calendar"></i> Rendez-vous</a>
    <a href="#"><i class="fas fa-book"></i> Ressources Éducatives</a>
    <a href="#"><i class="fas fa-comment-medical"></i> Consultation</a>
    <a href="#"><i class="fas fa-comments"></i> Forum</a>
    <a href="/programme_admin.php" class="active"><i class="fas fa-tasks"></i> Programme</a>
    <a href="#"><i class="fas fa-cog"></i> Settings</a>
</div>

<div class="main-content">
    <h1>Edit Programme</h1>
    
    <div class="card">
        <div class="card-body">
            <form action="programme_edit.php?id=<?= $id ?>" method="post">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($programme['nom']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?= date('Y-m-d', strtotime($programme['date'])) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="progression" class="form-label">Progression</label>
                    <input type="text" class="form-control" id="progression" name="progression" 
                           value="<?= htmlspecialchars($programme['progression']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="evaluation" class="form-label">Evaluation</label>
                    <input type="number" class="form-control" id="evaluation" name="evaluation" 
                           value="<?= htmlspecialchars($programme['evaluation']) ?>">
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="programme_admin.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="edit_programme" class="btn btn-primary">Update Programme</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 