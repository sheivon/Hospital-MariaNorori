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
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet"> 
  <link rel="stylesheet" href="/assets/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="/assets/vendor/datatables/buttons.dataTables.min.css">
  <!-- SweetAlert v1 CSS -->
  <link href="/assets/css/sweetalert.min.css" rel="stylesheet">
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
      top: 0 ;
      right: 0.03rem;
      z-index: 1050;
      background: rgba(255, 255, 255, 0.4);
      border: 1px solid rgba(6, 5, 5, 0.05);
      backdrop-filter: blur(8px);
      box-shadow: 0 0.05rem 0.1rem rgba(0,0,0,0.08);
      min-width: 120px;
    }
    .floating-lang-select .form-select {
      min-width: 4rem;
      padding-right: 0.5rem;
    }
    @media (max-width: 992px) {
      .floating-lang-select {
        top: 0.2rem;
        right: 0.2rem;
        min-width: 100px;
      }
    }
  </style>
</head>
<body class="lang-loading<?= !empty($hideSidebar) ? ' hide-sidebar' : '' ?>" style="visibility:hidden;">
<div class="app-shell">
  <?php if (empty($hideSidebar)): ?>
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
      <?php if (!empty($_SESSION['user'])):
            $userRole = strtolower($_SESSION['user']['role'] ?? 'user');
            $modules = \App\Core\ModuleRegistry::visibleForRole($userRole);
          ?>
          <li class="nav-item"><a class="nav-link" href="/"><i class="fa-solid fa-chart-line me-2"></i><span data-i18n="dashboard">Dashboard</span></a></li>
          <?php foreach ($modules as $module):
            $submenuId = htmlspecialchars($module->getSlug() . 'Submenu', ENT_QUOTES);
            $subitems = $module->getSubItems();
          ?>
            <?php if (count($subitems) > 0): ?>
              <li class="nav-item">
                <a class="nav-link collapsed d-flex align-items-center" href="#<?= $submenuId ?>" data-bs-toggle="collapse" data-bs-target="#<?= $submenuId ?>" role="button" aria-expanded="false" aria-controls="<?= $submenuId ?>">
                  <i class="fa-solid <?= htmlspecialchars($module->getIcon()) ?> me-2"></i>
                  <span data-i18n="<?= htmlspecialchars($module->getLabelKey()) ?>"><?= htmlspecialchars($module->getLabel()) ?></span>
                  <i class="fa-solid fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="<?= $submenuId ?>">
                  <ul class="nav flex-column ps-4">
                    <?php foreach ($subitems as $item): ?>
                      <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($item['path']) ?>"><span<?= !empty($item['labelKey']) ? ' data-i18n="' . htmlspecialchars($item['labelKey']) . '"' : '' ?>><?= htmlspecialchars($item['label']) ?></span></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= htmlspecialchars($module->getPath()) ?>">
                  <i class="fa-solid <?= htmlspecialchars($module->getIcon()) ?> me-2"></i>
                  <span data-i18n="<?= htmlspecialchars($module->getLabelKey()) ?>"><?= htmlspecialchars($module->getLabel()) ?></span>
                </a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
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
  <?php endif; ?>
  <main class="main-content<?= !empty($hideSidebar) ? ' full-width' : '' ?>">
    <?php if (empty($hideLanguageSelect)): ?>
    <div class="floating-lang-select shadow-sm rounded d-flex align-items-center gap-2 p-2">
      <i class="fa-solid fa-language"></i>
      <select id="langSelect" class="form-select form-select-sm">
        <option value="en">EN</option>
        <option value="es">ES</option>
      </select>
    </div>
    <?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const langSelect = document.getElementById('langSelect');
        const userLang = localStorage.getItem('lang') || 'en';
        if (langSelect) {
          langSelect.value = userLang;
        }
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
        if (langSelect) {
          langSelect.addEventListener('change', async () => {
              const selectedLang = langSelect.value;
              localStorage.setItem('lang', selectedLang);
              document.documentElement.lang = selectedLang;
              await loadLanguage(selectedLang);
          });
        }

        // Load initial language
        await loadLanguage(userLang);
        // Show body only after translation completes (prevent icon/text flicker)
        document.body.classList.remove('lang-loading');
        document.body.style.visibility = 'visible';
    });
</script>
