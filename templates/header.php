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
  <title data-i18n="hospital">Hospital Records</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet"> 
  <link rel="stylesheet" href="../assets/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="../assets/css/buttons.dataTables.min.css">
  <!-- SweetAlert v1 CSS -->
  <link href="../assets/css/sweetalert.min.css" rel="stylesheet">
  <!-- Font Awesome (free) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- assets are served from the web root (public/), so drop the /public prefix -->
  <link href="/assets/css/styles.css" rel="stylesheet">
  <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), ['login.php', 'register.php'], true)): ?>
    <link href="/assets/css/auth.css" rel="stylesheet">
  <?php endif; ?>
  <style>body.lang-loading{visibility:hidden;}</style>
  <?php
  // Load DataTables Buttons CSS if present (local offline copy)
  $btnCssPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/vendor/datatables/buttons.dataTables.min.css';
  if (file_exists($btnCssPath)) {
    echo "<link href=\"/assets/vendor/datatables/buttons.dataTables.min.css\" rel=\"stylesheet\">\n";
  }
  ?>
</head>
<body class="lang-loading" style="visibility:hidden;">
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="/">
      <picture class="me-2">
        <!-- prefer the SVG; PNG was converted to an embedded-SVG file -->
        <source srcset="/assets/images/minsa-logo.svg" type="image/svg+xml">
        <img src="/assets/images/minsa-logo.svg" alt="MINSA logo" height="36" style="display:block;" />
      </picture>
      <span class="brand-text" data-i18n="hospital">Hospital</span>
    </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-center">
      <?php if (!empty($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="/patients.php"><i class="fa-solid fa-users me-1"></i><span data-i18n="patients">Patients</span></a></li>
          <li class="nav-item"><a class="nav-link" href="/diagnostics.php"><i class="fa-solid fa-stethoscope me-1"></i><span data-i18n="diagnostics_title">Diagnostics</span></a></li>
          <?php if (!empty($_SESSION['user']['role']) && strtolower($_SESSION['user']['role'])==='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/admin/users.php"><i class="fa-solid fa-user-shield me-1"></i><span data-i18n="admin_users">Admin</span></a></li>
            <li class="nav-item"><a class="nav-link" href="/admin/data_manager.php"><i class="fa-solid fa-table-list me-1"></i><span data-i18n="data_manager_title">Data Manager</span></a></li>
          <?php endif; ?>
          
          <!-- User dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-user me-1"></i>
              <span class="username"><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="/profile.php"><i class="fa-solid fa-user me-1"></i><span data-i18n="profile_title">Profile</span></a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="/logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i><span data-i18n="logout">Logout</span></a></li>
            </ul>
          </li>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link" href="/login.php" data-i18n="sign_in">Sign in</a>
        </li>
       <?php endif; ?>
        <hr class="pl-2"/>   <i class="fa-solid fa-language"></i>
        <li class="nav-item ms-2 d-inline">   
          <select id="langSelect" class="form-select form-select-sm btn-acrylic">
            <option value="en" class="btn-acrylic text-dark">EN</option>
            <option value="es" class="btn-acrylic text-dark">ES</option>
          </select>
        </li>
      </ul>
    </div>
  </div>
</nav>

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
