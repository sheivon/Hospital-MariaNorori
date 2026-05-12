<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">
      <i class="fa-solid fa-stethoscope me-2"></i>
      <span data-i18n="diagnostics_title">Diagnostics</span>
    </h2>
    <div class="d-flex align-items-center gap-2">
      <label class="me-2" for="diagPatientFilter" data-i18n="patient">Patient</label>
      <select id="diagPatientFilter" class="form-select" style="min-width:260px"></select>
      <button id="btnDiagAdd" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="diagnostics_add_btn">Add Diagnostic</span></button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" id="diagnosticsTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="diagnostics_type">Type</th>
              <th data-i18n="diagnostics_description">Description</th>
              <th data-i18n="diagnostics_date">Date</th>
              <th data-i18n="diagnostics_created_by">Created by</th>
          <th data-i18n="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const filter = document.getElementById('diagPatientFilter');
  const btnAdd = document.getElementById('btnDiagAdd');
  const tbl = document.querySelector('#diagnosticsTable tbody');
  let diagnosticsTable = null;

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function initDiagnosticsTable() {
    if (typeof $ === 'undefined' || !$.fn.dataTable) {
      window.addEventListener('load', initDiagnosticsTable, { once: true });
      return;
    }
    if (diagnosticsTable) {
      diagnosticsTable.destroy();
      diagnosticsTable = null;
    }
    diagnosticsTable = $('#diagnosticsTable').DataTable({
      responsive: true,
      pageLength: 10
    });
  }

  async function loadPatients(){
    try {
      const res = await fetch('/api/patients_list.php?encountered=1', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return [];
      const patients = Array.isArray(json.data) ? json.data : [];
      filter.innerHTML = `<option value="">${t('all')||'All patients'}</option>` + patients.map(p=>{
        const name = `${p.first_name} ${p.last_name}`.trim();
        return `<option value="${encodeURIComponent(p.id)}">${escapeHtml(name)}</option>`;
      }).join('');
      return patients;
    } catch (e) {
      return [];
    }
  }

  async function loadDiagnostics(){
    const pid = filter.value;
    const url = pid ? `/api/diagnostics_list.php?patient_id=${pid}` : '/api/diagnostics_list.php';
    tbl.innerHTML = `<tr><td colspan="6">${t('loading')||'Loading...'}</td></tr>`;
    try {
      const res = await fetch(url, { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){
        tbl.innerHTML = `<tr><td colspan="6">${escapeHtml(json.error || t('error') || 'Error')}</td></tr>`;
        return;
      }
      const rows = Array.isArray(json.diagnostics) ? json.diagnostics : [];
      if (rows.length === 0){
        tbl.innerHTML = `<tr><td colspan="6">${t('diagnostics_table_empty')||'No diagnostics found'}</td></tr>`;
        return;
      }
      tbl.innerHTML = '';
      rows.forEach((d,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i+1}</td>
          <td>${escapeHtml(d.type)}</td>
          <td>${escapeHtml(d.description||'')}</td>
          <td>${d.date||''}</td>
          <td>${escapeHtml(d.created_by_name||'')}</td>
          <td>
            <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${d.id}" title="${t('edit')||'Edit'}">
              <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${t('edit')||'Edit'}</span>
            </button>
          </td>
        `;
        tbl.appendChild(tr);
      });
      initDiagnosticsTable();
    } catch (e) {
      tbl.innerHTML = `<tr><td colspan="6">${t('error')||'Error'}</td></tr>`;
    }
  }

  tbl.addEventListener('click', e=>{
    const btn = e.target.closest('button');
    if (!btn) return;
    if (btn.classList.contains('btn-edit')){
      const id = btn.dataset.id;
      if (id) window.location.href = '/diagnostic.php?id=' + encodeURIComponent(id);
    }
  });

  btnAdd.addEventListener('click', ()=>{
    window.location.href = '/diagnostic.php';
  });

  filter.addEventListener('change', loadDiagnostics);

  loadPatients().then(()=> loadDiagnostics());
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
