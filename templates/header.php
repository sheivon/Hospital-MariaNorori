<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title data-i18n="hospital">Centro Salud</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet"> 
  <link rel="stylesheet" href="../assets/css/jquery.dataTables.min.css">
  <!-- DataTables Buttons CSS loaded from vendor if available -->
  <?php
  $btnCssPath1 = $_SERVER['DOCUMENT_ROOT'] . '/assets/css/buttons.dataTables.min.css';
  $btnCssPath2 = $_SERVER['DOCUMENT_ROOT'] . '/assets/vendor/datatables/buttons.dataTables.min.css';
  if (file_exists($btnCssPath1)) {
    echo "<link rel=\"stylesheet\" href=\"/assets/css/buttons.dataTables.min.css\">\n";
  } elseif (file_exists($btnCssPath2)) {
    echo "<link rel=\"stylesheet\" href=\"/assets/vendor/datatables/buttons.dataTables.min.css\">\n";
  }
  ?>
  <!-- SweetAlert v1 CSS -->
  <link href="../assets/css/sweetalert.min.css" rel="stylesheet">
  <!-- Font Awesome (free) -->
  <link href="/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
  <!-- assets are served from the web root (public/), so drop the /public prefix -->
  <link href="/assets/css/styles.css" rel="stylesheet">
  <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), ['login.php', 'register.php'], true)): ?>
    <link href="/assets/css/auth.css" rel="stylesheet">
  <?php endif; ?>
  <style>
    body.lang-loading{visibility:hidden;}
    .floating-lang-select {
      position: fixed;
      top: 0.1rem;
      right: 0.1rem;
      z-index: 1050;
      background: rgba(255,255,255,0.95);
      border: 1px solid rgba(0,0,0,0.08);
      backdrop-filter: blur(8px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
      min-width: 120px;
    }
    .floating-lang-select .form-select {
      min-width: 4rem;
      padding-right: 0.75rem;
    }
    @media (max-width: 992px) {
      .floating-lang-select {
        top: 0.75rem;
        right: 0.75rem;
        min-width: 100px;
      }
    }
  </style>
  <?php
  // Load DataTables Buttons CSS if present (local offline copy)
  $btnCssPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/vendor/datatables/buttons.dataTables.min.css';
  if (file_exists($btnCssPath)) {
    echo "<link href=\"/assets/vendor/datatables/buttons.dataTables.min.css\" rel=\"stylesheet\">\n";
  }
  ?>
</head>
<body class="lang-loading" style="visibility:hidden;">
<div class="app-shell">
  <aside class="sidebar sidebar-custom">
    <div class="sidebar-top d-flex align-items-center p-4">
      <a class="navbar-brand d-flex flex-column align-items-center text-center" href="/">
        <picture class="d-sm-none d-md-block d-lg-block" style="z-index:1000;">
          <!-- prefer the SVG; PNG was converted to an embedded-SVG file -->
          <source srcset="/assets/images/minsa-logo.svg" type="image/svg+xml">
          <img src="/assets/images/minsa-logo.svg" alt="MINSA logo" height="36" style="display:block;" />
        </picture>
        <span class="brand-text mt-1" data-i18n="hospital">Centro Salud</span>
      </a>
      <button class="btn btn-sm btn-light d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav" aria-expanded="false" aria-label="Toggle menu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
    <div class="collapse d-md-block" id="sidebarNav">
      <ul class="nav flex-column px-2">
      <?php if (!empty($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="/dashboard.php"><i class="fa-solid fa-chart-line me-2"></i><span data-i18n="dashboard">Dashboard</span></a></li>
          <li class="nav-item">
            <a class="nav-link collapsed d-flex align-items-center" href="#patientsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="patientsSubmenu">
              <i class="fa-solid fa-users me-2"></i><span data-i18n="patients">Patients</span>
              <i class="fa-solid fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse" id="patientsSubmenu">
              <ul class="nav flex-column ps-4">
                <li class="nav-item"><a class="nav-link" href="/pacientes.php"><span data-i18n="view_patients">Ver pacientes</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/tests.php"><span data-i18n="tests_results">Test Results</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/adolescent_history.php"><span data-i18n="adolescent_history">Adolescent History</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/allergis.php"><span data-i18n="allergies">Allergies</span></a></li>
                <li class="nav-item"><a class="nav-link" href="/diagnostics.php"><span data-i18n="diagnostics_title">Diagnostics</span></a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link collapsed d-flex align-items-center" href="#examenesSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="examenesSubmenu">
              <i class="fa-solid fa-vials me-2"></i><span>Exámenes</span>
              <i class="fa-solid fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse" id="examenesSubmenu">
              <ul class="nav flex-column ps-4">
                <li class="nav-item"><a class="nav-link" href="/examen.php">Exámenes</a></li>
                <li class="nav-item"><a class="nav-link" href="/solicitud_de_examen.php">Solicitud de examen</a></li>
                <li class="nav-item"><a class="nav-link" href="/radiologia.php">Radiología</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link collapsed d-flex align-items-center" href="#encountersSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="encountersSubmenu">
              <i class="fa-solid fa-notes-medical me-2"></i><span data-i18n="encounters">Encounters</span>
              <i class="fa-solid fa-chevron-down ms-auto"></i>
            </a>
            <div class="collapse" id="encountersSubmenu">
              <ul class="nav flex-column ps-4">
                <li class="nav-item"><a class="nav-link" href="/encounters.php">Encounters</a></li>
                <li class="nav-item"><a class="nav-link" href="/emergency.php">Emergency</a></li>
              </ul>
            </div>
          </li>
          <?php if (!empty($_SESSION['user']['role']) && strtolower($_SESSION['user']['role'])==='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/admin/users.php"><i class="fa-solid fa-user-shield me-2"></i><span data-i18n="admin_users">Admin</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/admin/data_manager.php"><i class="fa-solid fa-table-list me-2"></i><span data-i18n="data_manager_title">Data Manager</span></a></li>
          <?php endif; ?>
          <li class="nav-item dropdown mt-2">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-user me-2"></i>
              <span class="username"><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="/profile.php"><i class="fa-solid fa-user me-1"></i><span data-i18n="profile_title">Profile</span></a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="/logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i><span data-i18n="logout">Logout</span></a></li>
            </ul>
          </li>
      <?php else: ?>
        <li class="nav-item gap-2 mr-auto"><a class="nav-link" href="/login.php" data-i18n="sign_in">Sign in</a></li>
      <?php endif; ?>
      </ul>
    </div>
  </aside>
  <main class="main-content">
    <div class="floating-lang-select shadow-sm rounded d-flex align-items-center gap-2 p-2">
      <i class="fa-solid fa-language"></i>
      <select id="langSelect" class="form-select form-select-sm">
        <option value="en">EN</option>
        <option value="es">ES</option>
      </select>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const langSelect = document.getElementById('langSelect');
        const userLang = localStorage.getItem('lang') || 'en';
        langSelect.value = userLang;
        document.documentElement.lang = userLang;

        // Load language file dynamically
        const loadLanguage = async (lang) => {
            const response = await fetch(`/assets/i18n/${lang}.json`);
            const translations = await response.json();
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (!translations[key]) return;

                // Preserve icon elements if present
                const icon = el.querySelector('i');
                if (icon) {
                    // Keep icon(s), replace only the text portion
                    const iconHtml = icon.outerHTML;
                    el.innerHTML = iconHtml + ' ' + translations[key];
                } else {
                    el.textContent = translations[key];
                }
            });

            // Support translating placeholders, titles, and values
            document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
                const key = el.getAttribute('data-i18n-placeholder');
                if (translations[key]) {
                    el.setAttribute('placeholder', translations[key]);
                }
            });
            document.querySelectorAll('[data-i18n-title]').forEach(el => {
                const key = el.getAttribute('data-i18n-title');
                if (translations[key]) {
                    el.setAttribute('title', translations[key]);
                }
            });
            document.querySelectorAll('[data-i18n-value]').forEach(el => {
                const key = el.getAttribute('data-i18n-value');
                if (translations[key]) {
                    el.value = translations[key];
                }
            });
        };

        // Change language on selection
        langSelect.addEventListener('change', async () => {
            const selectedLang = langSelect.value;
            localStorage.setItem('lang', selectedLang);
            document.documentElement.lang = selectedLang;
            await loadLanguage(selectedLang);
        });

        // Load initial language
        await loadLanguage(userLang);
        // Show body only after translation completes (prevent icon/text flicker)
        document.body.classList.remove('lang-loading');
        document.body.style.visibility = 'visible';
    });
</script>
