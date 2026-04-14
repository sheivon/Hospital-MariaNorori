<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4 print-container">
  <div class="print-header d-flex align-items-center mb-3">
    <img src="/assets/images/Logo-01.png" alt="Logo" style="max-height: 70px; margin-right: 1rem;" />
    <div>
      <h1 id="printPageTitle" data-i18n="print_view_title">Print View</h1>
      <p id="printPageSubtitle" class="text-muted" data-i18n="printing_full_dataset_for"></p>
    </div>
  </div>

  <div class="card mb-3" id="printStatus" style="display:none;">
    <div class="card-body">
      <p id="printStatusText" data-i18n="loading">Loading...</p>
    </div>
  </div>

  <table class="table table-sm table-striped table-bordered print-table" id="printDataTable">
    <thead>
      <tr id="printHeaderRow"></tr>
    </thead>
    <tbody id="printBody"></tbody>
  </table>
</div>

<script>
(function(){
  const renderPrintPage = () => {
    const params = new URLSearchParams(window.location.search);
    const resource = params.get('resource') || 'users';
    const mapping = {
      users: 'users',
      patients: 'patients',
      encounters: 'encounters'
    };

    const key = mapping[resource] || 'data';
    const baseTitle = window.i18n_t ? window.i18n_t(key) : key;
    document.getElementById('printPageTitle').textContent = window.i18n_t ? window.i18n_t('print_view_title', { resource: baseTitle }) : baseTitle + ' Print View';
    document.getElementById('printPageSubtitle').textContent = window.i18n_t ? window.i18n_t('printing_full_dataset_for', { resource: baseTitle }) : `Printing full dataset for ${baseTitle}`;

    const status = document.getElementById('printStatus');
    const statusText = document.getElementById('printStatusText');
    status.style.display = 'block';

    const header = document.getElementById('printHeaderRow');
    const body = document.getElementById('printBody');

    fetch('/api/print_data.php?resource=' + encodeURIComponent(resource), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(json => {
        if (!json.success) {
          statusText.textContent = json.error || (window.i18n_t ? window.i18n_t('error') : 'Error loading data');
          return;
        }

        const data = json.data || {};
        const cols = Array.isArray(data.columns) ? data.columns : [];
        const rows = Array.isArray(data.rows) ? data.rows : [];

        if (!cols.length) {
          statusText.textContent = window.i18n_t ? window.i18n_t('no_columns_to_print') : 'No columns to print';
          return;
        }

        status.style.display = 'none';
        header.innerHTML = cols.map(c => `<th>${c.label}</th>`).join('');
        body.innerHTML = rows.map(r => {
          const cells = cols.map(c => {
            const value = r[c.field] ?? '';
            return `<td>${String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}</td>`;
          }).join('');
          return `<tr>${cells}</tr>`;
        }).join('');

        setTimeout(() => { window.print(); }, 300);
      })
      .catch(e => {
        statusText.textContent = window.i18n_t ? window.i18n_t('unable_load_print_data', { error: e.message || e }) : 'Unable to load print data: ' + (e.message || e);
      });
  };

  document.addEventListener('DOMContentLoaded', renderPrintPage);
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
