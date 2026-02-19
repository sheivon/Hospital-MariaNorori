<?php
// setup.php - Public setup page for database initialization (no login required)
// This page allows creating the database and tables without authentication
// WARNING: This is a security risk in production - remove or protect this page after setup

include __DIR__ . '/../templates/header.php';

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
        'DB_HOST' => getenv('DB_HOST') ?: '192.168.1.204',
        'DB_USER' => getenv('DB_USER') ?: 'Marianorori',
        'DB_PASS' => getenv('DB_PASS') ?: 'SuperNoror!26*',
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
    $migrationsDir = __DIR__ . '/../migrations';
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

// Function to drop all tables
function dropTables($pdo) {
    $tables = [
        'diagnostics',
        'tests', 
        'visits',
        'chat_messages',
        'patients',
        'users'
    ];
    $results = [];
    foreach ($tables as $table) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS `$table`");
            $results[] = "Dropped table: $table";
        } catch (PDOException $e) {
            $results[] = "Error dropping table '$table': " . $e->getMessage();
        }
    }
    return $results;
}

// Function to run scripts (mimics admin maintenance scripts)
function runScripts() {
    $scriptsDir = __DIR__ . '/../scripts';
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

// Function to create default admin user (single user)
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

// Function to create default users
function createDefaultUsers($pdo) {
    $defaultUsers = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'fullname' => 'Administrator',
            'role' => 'admin'
        ],
        [
            'username' => 'doctor',
            'password' => 'doctor123',
            'fullname' => 'Dr. Smith',
            'role' => 'doctor'
        ]
    ];
    $results = [];
    foreach ($defaultUsers as $user) {
        // Check if user exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $user['username']]);
        if ($stmt->fetch()) {
            $results[] = "User '{$user['username']}' already exists.";
            continue;
        }
        // Insert user
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, fullname, role) VALUES (:u, :p, :f, :r)');
        try {
            $stmt->execute([
                ':u' => $user['username'],
                ':p' => $hash,
                ':f' => $user['fullname'], 
                ':r' => $user['role']
            ]);
            $results[] = "Created user: {$user['username']} (password: {$user['password']})";
        } catch (PDOException $e) {
            $results[] = "Error creating user '{$user['username']}': " . $e->getMessage();
        }
    }
    return $results;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_db'])) {
        $message .= createDatabase() . "\n";
    } elseif (isset($_POST['create_tables'])) {
        // Assume DB exists, try to connect
        try {
            require_once __DIR__ . '/../config/db.php';
            $message .= implode("\n", runMigrations($pdo)) . "\n";
            $message .= "Tables created successfully.";
        } catch (Exception $e) {
            $message .= "Error connecting to database. Make sure the database exists first. " . $e->getMessage();
        }
    } elseif (isset($_POST['create_users'])) {
        // Assume DB and tables exist
        try {
            require_once __DIR__ . '/../config/db.php';
            $results = createDefaultUsers($pdo);
            $message .= implode("\n", $results);
        } catch (Exception $e) {
            $message .= "Error creating users. Make sure the database and tables exist first. " . $e->getMessage();
        }
    } elseif (isset($_POST['drop_tables'])) {
        // Assume DB exists
        try {
            require_once __DIR__ . '/../config/db.php';
            $results = dropTables($pdo);
            $message .= implode("\n", $results);
        } catch (Exception $e) {
            $message .= "Error dropping tables. Make sure the database exists. " . $e->getMessage();
        }
    } elseif (isset($_POST['initialize_db'])) {
        // Full initialize: create DB, run migrations, create admin, run scripts
        $message .= createDatabase() . "\n";
        try {
            require_once __DIR__ . '/../config/db.php';
            $message .= implode("\n", runMigrations($pdo)) . "\n";
            $message .= createDefaultAdmin($pdo) . "\n";
            $message .= implode("\n", runScripts()) . "\n";
            $message .= "Database initialization completed.";
        } catch (Exception $e) {
            $message .= "Error during initialization: " . $e->getMessage();
        }
    } elseif (isset($_POST['create_admin'])) {
        try {
            require_once __DIR__ . '/../config/db.php';
            $message .= createDefaultAdmin($pdo);
        } catch (Exception $e) {
            $message .= "Error creating admin: " . $e->getMessage();
        }
    }
}
?>
<div class="container mt-4">
  <h3>Database Setup</h3>
  <p class="text-warning">This page is for initial setup only. Remove or protect it after use!</p>
  <?php if ($message): ?>
    <div class="alert alert-info">
      <pre><?php echo htmlspecialchars($message); ?></pre>
    </div>
  <?php endif; ?>
  <div class="card">
    <div class="card-body">
      <h5>Create Database</h5>
      <p>Creates the 'hospital' database if it doesn't exist.</p>
      <form method="post">
        <button type="submit" name="create_db" class="btn btn-primary">Create Database</button>
      </form>
    </div>
  </div>
  <div class="card mt-3">
    <div class="card-body">
      <h5>Create Tables</h5>
      <p>Runs all migrations to create tables in the required order for foreign key relations. Requires the database to exist.</p>
      <form method="post" class="d-inline">
        <button type="submit" name="create_tables" class="btn btn-success">Create Tables</button>
      </form>
      <form method="post" class="d-inline ms-2">
        <button type="submit" name="drop_tables" class="btn btn-danger" onclick="return confirm('Are you sure you want to drop all tables? This will delete all data!')">Drop Tables</button>
      </form>
    </div>
  </div>
  <div class="card mt-3">
    <div class="card-body">
      <h5>Create Default Users</h5>
      <p>Creates default admin and doctor users with predefined passwords. Requires the database and tables to exist.</p>
      <form method="post">
        <button type="submit" name="create_users" class="btn btn-warning">Create Default Users</button>
      </form>
    </div>
  </div>
<?php include __DIR__ . '/../templates/footer.php'; ?>