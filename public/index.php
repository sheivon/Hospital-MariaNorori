<?php
// Public landing page. Offers Sign in and optional links. Dashboard moved to /patients.php
include __DIR__ . '/../templates/header.php';
?>
<div class="py-5 text-center">
  
<div class="container mt-4">
  <h1 class="display-4" data-i18n="hospital">Hospital</h1>
  <p class="lead" data-i18n="welcome_message">Welcome to the Hospital Records system.</p>
</div>
  <div class="container"> 
    <div class="d-flex justify-content-center gap-2 mt-3">
  <?php
      if (empty($_SESSION['user'])) {
          echo '
              <a href="/login.php" class="btn btn-primary btn-lg" data-i18n="btn_login">Sign in</a>
              <a href="/patients.php" class="btn btn-outline-primary btn-lg" data-i18n="go_patients">Patients (dashboard)</a>
              <a href="/demo.php" class="btn btn-outline-secondary btn-lg" data-i18n="go_demo">Try demo</a>
          ';
      } else {
          echo '
              <a href="/patients.php" class="btn btn-outline-primary btn-lg" data-i18n="go_patients">Patients (dashboard)</a>
          ';
      }
      ?>


    </div>
   </div>
</div>

<!-- Overlay for loading with circular progress -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; justify-content: center; align-items: center;">
    <div style="width: 50px; height: 50px; border: 5px solid rgba(255, 255, 255, 0.3); border-top: 5px solid white; border-radius: 50%; animation: spin 1s linear infinite;"></div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

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

    document.addEventListener('DOMContentLoaded', () => {
        const userLang = localStorage.getItem('lang') || 'en';

        const loadLanguage = async (lang) => {
            const response = await fetch(`/assets/i18n/${lang}.json`);
            const translations = await response.json();
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[key]) {
                    el.textContent = translations[key];
                }
            });
        };

        // Load initial language
        loadLanguage(userLang);
    });
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
            <h2 class="card-head m-2 p2" data-i18n="patients">Patients</h2>
            <div class="card-body">
                <p data-i18n="patient_descriptions">Manage patient records and information.</p>
                <h3><?php echo $counts['patient_count']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h2 class="card-head m-2 p2" data-i18n="users">Users</h2>
            <div class="card-body">
                <p data-i18n="users_description">Manage user accounts and permissions.</p>
                <h3><?php echo $counts['user_count']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <h2 class="card-head m-2 p2" data-i18n="visits">Visits</h2>
            <div class="card-body">
                <p data-i18n="visits_description">Track patient visits and appointments.</p>
                <h3><?php echo $counts['visit_count']; ?></h3>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php';
