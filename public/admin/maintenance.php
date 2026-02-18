<?php
require_once __DIR__ . '/../../src/auth.php';
require_role('admin');
include __DIR__ . '/../../templates/header.php';

// Function to execute SQL file
function executeSqlFile($pdo, $filePath) {
    if (!file_exists($filePath)) {
        return "File not found: $filePath";
    }
    $sql = file_get_contents($filePath);
    try {
        $pdo->exec($sql);
        return "Executed: " . basename($filePath);
    } catch (PDOException $e) {
        return "Error in " . basename($filePath) . ": " . $e->getMessage();
    }
}

// Function to create database
function createDatabase() {
    $config = [
        'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
        'DB_USER' => getenv('DB_USER') ?: 'root',
        'DB_PASS' => getenv('DB_PASS') ?: 'Kilabone15*',
    ];
    $dsn = "mysql:host={$config['DB_HOST']};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        return "Database 'hospital' created or already exists.";
    } catch (PDOException $e) {
        return "Error creating database: " . $e->getMessage();
    }
}

// Function to run all migrations
function runMigrations($pdo) {
    $migrationsDir = __DIR__ . '/../../migrations';
    $files = [
        'init.sql',
        '20251101_create_chat_messages.sql',
        '20251102_add_cedula.sql',
        '20251102_add_patient_email.sql',
        '20251102_create_diagnostics.sql',
        '20251103_add_chat_recipient.sql',
        'create_tests_table.sql',
    ];
    $results = [];
    foreach ($files as $file) {
        $results[] = executeSqlFile($pdo, $migrationsDir . '/' . $file);
    }
    return $results;
}

// Function to run scripts
function runScripts() {
    $scriptsDir = __DIR__ . '/../../scripts';
    $scripts = [
        'add_cedula_columns.php',
        'add_cedula_unique.php',
        'add_patient_email.php',
        'add_chat_recipient.php',
        'create_diagnostics_table.php',
        'create_tests_table.php',
        'create_visits_table.php',
    ];
    $results = [];
    foreach ($scripts as $script) {
        $filePath = $scriptsDir . '/' . $script;
        if (file_exists($filePath)) {
            ob_start();
            include $filePath;
            $output = ob_get_clean();
            $results[] = "Ran: $script - Output: $output";
        } else {
            $results[] = "Script not found: $script";
        }
    }
    return $results;
}

// Function to create default admin
function createDefaultAdmin($pdo) {
    $username = 'admin';
    $password = 'admin123';
    $fullname = 'Administrator';
    $cedula = null;
    $role = 'admin';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT IGNORE INTO users (username, password, fullname, cedula, role) VALUES (:u,:p,:f,:cedula,:r)');
    try {
        $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$fullname,':cedula'=>$cedula,':r'=>$role]);
        return "Default admin user created: $username / $password";
    } catch (PDOException $e) {
        return "Error creating admin: " . $e->getMessage();
    }
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['initialize_db'])) {
        $message .= createDatabase() . "\n";
        // Now connect to db
        require_once __DIR__ . '/../../config/db.php';
        $message .= implode("\n", runMigrations($pdo)) . "\n";
        $message .= createDefaultAdmin($pdo) . "\n";
        $message .= implode("\n", runScripts()) . "\n";
        $message .= "Database initialization completed.";
    } elseif (isset($_POST['create_admin'])) {
        require_once __DIR__ . '/../../config/db.php';
        $message .= createDefaultAdmin($pdo);
    }
}
?>
<div class="container mt-4">
  <h3>Database Maintenance</h3>
  <?php if ($message): ?>
    <div class="alert alert-info">
      <pre><?php echo htmlspecialchars($message); ?></pre>
    </div>
  <?php endif; ?>
  <div class="card">
    <div class="card-body">
      <h5>Initialize Database from Scratch</h5>
      <p>This will create the database, run all migrations, and execute setup scripts. <strong>Warning: This may overwrite existing data!</strong></p>
      <form method="post">
        <button type="submit" name="initialize_db" class="btn btn-danger" onclick="return confirm('Are you sure? This will reset the database!')">Initialize Database</button>
      </form>
    </div>
  <div class="card mt-3">
    <div class="card-body">
      <h5>Create Default Admin User</h5>
      <p>Creates a default admin user: admin / admin123</p>
      <form method="post">
        <button type="submit" name="create_admin" class="btn btn-warning">Create Admin</button>
      </form>
    </div>
  </div>
<?php include __DIR__ . '/../../templates/footer.php'; ?>