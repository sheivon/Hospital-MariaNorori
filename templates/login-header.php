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
  <!-- SweetAlert v1 CSS -->
  <link href="../assets/css/sweetalert.min.css" rel="stylesheet">
  <!-- Font Awesome (free) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- assets are served from the web root (public/), so drop the /public prefix -->
  <link href="/assets/css/styles.css" rel="stylesheet">
  <?php
  // Load DataTables Buttons CSS if present (local offline copy)
  $btnCssPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/vendor/datatables/buttons.dataTables.min.css';
  if (file_exists($btnCssPath)) {
    echo "<link href=\"/assets/vendor/datatables/buttons.dataTables.min.css\" rel=\"stylesheet\">\n";
  }
  ?>
</head>
<body>
  
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const langSelect = document.getElementById('langSelect');
        const userLang = localStorage.getItem('lang') || 'en';
        langSelect.value = userLang;

        // Load language file dynamically
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

        // Change language on selection
        langSelect.addEventListener('change', async () => {
            const selectedLang = langSelect.value;
            localStorage.setItem('lang', selectedLang);
            await loadLanguage(selectedLang);
        });

        // Load initial language
        loadLanguage(userLang);
    });
</script>
