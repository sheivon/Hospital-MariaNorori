<div class="mt-4 print-container">
  <div class="print-header d-flex align-items-center mb-3">
    <img src="/assets/images/Logo-01.png" alt="Logo" style="max-height: 70px; margin-right: 1rem;" />
    <div>
      <h1 id="printPageTitle" data-i18n="print_view_title" data-i18n-resource="Print View">Print View</h1>
      <p id="printPageSubtitle" class="text-muted" data-i18n="printing_full_dataset_for" data-i18n-resource="Print View"></p>
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
    const lang = (localStorage.getItem('lang') || document.documentElement.lang || 'en') === 'es' ? 'es' : 'en';
    const t = window.i18n_t || (key => key);

    const titleEl = document.getElementById('printPageTitle');
    const subtitleEl = document.getElementById('printPageSubtitle');

    const status = document.getElementById('printStatus');
    const statusText = document.getElementById('printStatusText');
    status.style.display = 'block';

    const header = document.getElementById('printHeaderRow');
    const body = document.getElementById('printBody');

    const query = new URLSearchParams({ resource });
    query.set('lang', lang);
    if (params.get('patient_id')) {
      query.set('patient_id', params.get('patient_id'));
    }
    if (params.get('encounter_id')) {
      query.set('encounter_id', params.get('encounter_id'));
    }
    if (params.get('date_from')) {
      query.set('date_from', params.get('date_from'));
    }
    if (params.get('date_to')) {
      query.set('date_to', params.get('date_to'));
    }

    fetch('/api/print_data.php?' + query.toString(), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(json => {
        if (!json.success) {
          statusText.textContent = json.error || t('error');
          return;
        }

        const data = json.data || {};
        const cols = Array.isArray(data.columns) ? data.columns : [];
        const rows = Array.isArray(data.rows) ? data.rows : [];
        const metaTitle = data.title || resource;

        if (titleEl) {
          titleEl.textContent = t('print_view_title', { resource: metaTitle });
        }
        if (subtitleEl) {
          subtitleEl.textContent = t('printing_full_dataset_for', { resource: metaTitle });
        }

        if (!cols.length) {
          statusText.textContent = t('no_columns_to_print');
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
        statusText.textContent = t('unable_load_print_data', { error: e.message || e });
      });
  };

  document.addEventListener('DOMContentLoaded', renderPrintPage);
})();
</script>
