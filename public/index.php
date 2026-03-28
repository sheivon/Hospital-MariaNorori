<?php
// Public landing page. Offers Sign in and optional links. Dashboard moved to /patients.php
include __DIR__ . '/../templates/header.php';
?>
<div class="py-5 text-center">
  
<div class="container mt-4">
  <h1 class="display-4" data-i18n="hospital">Hospital</h1>
  <p class="lead" data-i18n="welcome_message">Bienvenido al sistema de registros del hospital.</p>
</div>
  <div class="container"> 
    <div class="d-flex justify-content-center gap-2 mt-3">
  <?php
      if (empty($_SESSION['user'])) {
          echo '
              <a href="/login.php" class="btn btn-primary btn-lg">
                  <i class="fa-solid fa-user-circle me-2"></i><span data-i18n="sign_in">Iniciar sesión</span>
              </a>
              <a href="/patients.php" class="btn btn-outline-primary btn-lg">
                  <i class="fa-solid fa-users"></i><span data-i18n="go_patients">Pacientes (panel)</span>
              </a> 
          ';
      } else {
          echo '
              <a href="/patients.php" class="btn btn-outline-primary btn-lg">
                  <i class="fa-solid fa-users me-2"></i><span data-i18n="go_patients">Pacientes (panel)</span>
              </a>
          ';
      }
      ?>


    </div>
   </div>
</div>

<?php include __DIR__ . '/../templates/loading_overlay.php'; ?>

<script>
    // Show overlay when loading data
    function showLoadingOverlay() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }

    // Hide overlay after loading data
    function hideLoadingOverlay() {
        document.getElementById('loading-overlay').style.display = 'none';
    }

    // Example: Fetch data from API and toggle overlay
    async function fetchData() {
        showLoadingOverlay();
        try {
            const response = await fetch('/api/patients_list.php');
            const data = await response.json();
            console.log(data); // Process data here
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            hideLoadingOverlay();
        }
    }

    // Trigger data fetch on page load
    window.onload = fetchData;
</script>

<?php
// Fetch counts from the database
require_once __DIR__ . '/../config/db.php';

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SHOW TABLES LIKE :table');
    $stmt->execute([':table' => $table]);
    return (bool)$stmt->fetchColumn();
}

function hasColumn(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
    $stmt->execute([':column' => $column]);
    return (bool)$stmt->fetchColumn();
}

function ensureSoftDeleteColumns(PDO $pdo, array $tables): void
{
    foreach ($tables as $table) {
        if (!tableExists($pdo, $table)) {
            continue;
        }

        if (!hasColumn($pdo, $table, 'deleted_at')) {
            $sql = sprintf("ALTER TABLE `%s` ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL", $table);
            $pdo->exec($sql);
        }

        // Ensure index exist in a safe way.
        $indexName = 'idx_' . $table . '_deleted_at';
        $indexExists = $pdo->query("SHOW INDEX FROM `$table` WHERE Key_name = '$indexName'")->fetch();
        if (!$indexExists) {
            $sql = sprintf("CREATE INDEX `%s` ON `%s` (`deleted_at`)", $indexName, $table);
            $pdo->exec($sql);
        }
    }
}

function countRows(PDO $pdo, string $table): int
{
    if (!tableExists($pdo, $table)) {
        return 0;
    }

    $sql = "SELECT COUNT(*) FROM `$table`";
    if (hasColumn($pdo, $table, 'deleted_at')) {
        $sql .= ' WHERE deleted_at IS NULL';
    }

    return (int)$pdo->query($sql)->fetchColumn();
}

try {
    ensureSoftDeleteColumns($pdo, ['users', 'patients', 'patient_contacts', 'encounters', 'patient_conditions', 'patient_allergies', 'diagnostics', 'tests', 'vitals', 'clinical_notes', 'treatment_plans', 'clinical_procedures', 'medications_catalog', 'prescriptions', 'treatment_administration', 'immunizations', 'appointments', 'admissions', 'bed_movements', 'chat_messages', 'audit_logs']);

    $visitLikeTable = tableExists($pdo, 'encounters') ? 'encounters' : 'visits';
    $counts = [
        'user_count' => countRows($pdo, 'users'),
        'patient_count' => countRows($pdo, 'patients'),
        'visit_count' => countRows($pdo, $visitLikeTable),
    ];
} catch (PDOException $e) {
    $counts = ['user_count' => 0, 'patient_count' => 0, 'visit_count' => 0];
}
?>

<div class="row p-0 m-0 mt-4 text-center">
    <div class="col-md-4">
        <div class="card">
            <h2 class="card-head m-2 p2"><i class="fa-solid fa-user-injured me-2"></i><span data-i18n="patients_title">Pacientes</span></h2>
            <div class="card-body">
                <p data-i18n="patient_descriptions">Gestiona los registros e información de los pacientes.</p>
                <h3><?php echo $counts['patient_count']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h2 class="card-head m-2 p2"><i class="fa-solid fa-user-shield me-2"></i><span data-i18n="users">Usuarios</span></h2>
            <div class="card-body">
                <p data-i18n="users_description">Gestiona las cuentas de usuario y sus permisos.</p>
                <h3><?php echo $counts['user_count']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h2 class="card-head m-2 p2"><i class="fa-solid fa-stethoscope me-2"></i><span data-i18n="visits">Visitas</span></h2>
            <div class="card-body">
                <p data-i18n="visits_description">Rastrea las visitas y citas de los pacientes.</p>
                <h3><?php echo $counts['visit_count']; ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php';
