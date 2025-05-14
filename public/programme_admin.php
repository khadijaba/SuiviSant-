<?php
// Simple standalone script for programme management
require_once dirname(__DIR__).'/vendor/autoload.php';

// Database connection parameters
$dbHost = 'localhost';
$dbUser = 'root';      // Update if different
$dbPass = '';          // Update if different
$dbName = 'pideva';    // Your database name

// Initialize variables
$programmes = [];
$debug_info = '';

// Function to log errors
function logError($message) {
    error_log($message, 3, dirname(__DIR__).'/var/log/programme_errors.log');
}

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Process form submission for adding a new programme
    if (isset($_POST['add_programme'])) {
        $nom = $_POST['nom'];
        $date = $_POST['date'];
        $progression = $_POST['progression'];
        
        $stmt = $pdo->prepare("INSERT INTO programme (nom, date, progression) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $date, $progression]);
        
        // Redirect to avoid form resubmission
        header("Location: programme_admin.php");
        exit;
    }
    
    // Process form submission for updating a programme
    if (isset($_POST['update_programme'])) {
        $oldNom = $_POST['old_nom']; // The original name to identify the record
        $nom = $_POST['nom'];
        $date = $_POST['date'];
        $progression = $_POST['progression'];
        
        try {
            // For debugging, save the values
            $debug_info = "Updating: Old nom=$oldNom, New nom=$nom, date=$date, progression=$progression<br>";
            
            // Try direct SQL query to update - might be more reliable
            $sql = "UPDATE programme SET nom = :nom, date = :date, progression = :progression WHERE nom = :oldNom";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':progression', $progression);
            $stmt->bindParam(':oldNom', $oldNom);
            $result = $stmt->execute();
            
            $affected = $stmt->rowCount();
            $debug_info .= "Update result: " . ($result ? "Success" : "Failed") . ", Affected rows: $affected<br>";
            $debug_info .= "SQL: $sql with values: nom=$nom, date=$date, progression=$progression, oldNom=$oldNom<br>";
            
            // If no rows affected, try to insert a new record instead
            if ($affected == 0) {
                $debug_info .= "No rows updated. Trying to insert new record...<br>";
                
                // First try to delete the old record
                $deleteStmt = $pdo->prepare("DELETE FROM programme WHERE nom = ?");
                $deleteStmt->execute([$oldNom]);
                
                // Then insert the new record
                $insertStmt = $pdo->prepare("INSERT INTO programme (nom, date, progression) VALUES (?, ?, ?)");
                $insertStmt->execute([$nom, $date, $progression]);
                
                $debug_info .= "Insert result: " . ($insertStmt->rowCount() > 0 ? "Success" : "Failed") . "<br>";
            }
        } catch (PDOException $e) {
            $debug_info .= "Error: " . $e->getMessage() . "<br>";
        }
    }
    
    // Process form submission for deleting a programme
    if (isset($_POST['delete_programme'])) {
        $nom = $_POST['nom'];
        
        $stmt = $pdo->prepare("DELETE FROM programme WHERE nom = ?");
        $stmt->execute([$nom]);
        
        // Redirect to avoid form resubmission
        header("Location: programme_admin.php");
        exit;
    }
    
    // Get selected programme for editing
    $selectedProgramme = null;
    if (isset($_GET['edit'])) {
        $editNom = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM programme WHERE nom = ?");
        $stmt->execute([$editNom]);
        $selectedProgramme = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Always get all programmes
    $stmt = $pdo->query("SELECT * FROM programme");
    $programmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $debug_info .= "Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
    <h1>Programme Management</h1>
    
    <?php if (!empty($debug_info)): ?>
        <div class="alert alert-info">
            <?= $debug_info ?>
            <a href="programme_admin.php" class="btn btn-primary">Continue</a>
        </div>
    <?php endif; ?>
    
    <?php if ($selectedProgramme): ?>
        <!-- Edit Programme Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Modifier le programme</h5>
            </div>
            <div class="card-body">
                <form action="programme_admin.php" method="post">
                    <input type="hidden" name="old_nom" value="<?= htmlspecialchars($selectedProgramme['nom']) ?>">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($selectedProgramme['nom']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d', strtotime($selectedProgramme['date'])) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="progression" class="form-label">Progression</label>
                        <input type="text" class="form-control" id="progression" name="progression" value="<?= htmlspecialchars($selectedProgramme['progression']) ?>" required>
                    </div>
                    <div class="d-flex">
                        <button type="submit" name="update_programme" class="btn btn-primary me-2">Mettre à jour</button>
                        <a href="programme_admin.php" class="btn btn-secondary">Annuler</a>
                        
                        <!-- Delete button -->
                        <div class="ms-auto">
                            <form method="post" action="programme_admin.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce programme?');" class="d-inline">
                                <input type="hidden" name="nom" value="<?= htmlspecialchars($selectedProgramme['nom']) ?>">
                                <button type="submit" name="delete_programme" class="btn btn-danger"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgrammeModal">
                <i class="fas fa-plus"></i> Add New Programme
            </button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Date</th>
                        <th>Progression</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($programmes) && count($programmes) > 0): ?>
                        <?php foreach ($programmes as $programme): ?>
                        <tr>
                            <td><?= htmlspecialchars($programme['nom']) ?></td>
                            <td><?= htmlspecialchars($programme['date']) ?></td>
                            <td><?= htmlspecialchars($programme['progression']) ?></td>
                            <td>
                                <a href="programme_admin.php?edit=<?= urlencode($programme['nom']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="post" action="programme_admin.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce programme?');" class="d-inline">
                                    <input type="hidden" name="nom" value="<?= htmlspecialchars($programme['nom']) ?>">
                                    <button type="submit" name="delete_programme" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No programmes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Programme Modal -->
<div class="modal fade" id="addProgrammeModal" tabindex="-1" aria-labelledby="addProgrammeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProgrammeModalLabel">Add New Programme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="programme_admin.php" method="post">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="progression" class="form-label">Progression</label>
                        <input type="text" class="form-control" id="progression" name="progression" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_programme" class="btn btn-primary">Save Programme</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 