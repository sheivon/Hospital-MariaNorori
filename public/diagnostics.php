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

<!-- Diagnostic Modal -->
<div class="modal fade" id="diagnosticModal" tabindex="-1" aria-labelledby="diagnosticModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="diagnosticModalTitle" data-i18n="diagnostics_add_btn">Add Diagnostic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="diagAlert" class="alert alert-danger d-none" role="alert"></div>
        <form id="diagForm" class="row g-3">
          <input type="hidden" id="diagId" name="id">

          <div class="col-md-6">
            <label for="diagPatient" class="form-label" data-i18n="patient">Patient</label>
            <select id="diagPatient" name="patient_id" class="form-select" required></select>
            <div class="invalid-feedback" id="diagPatientError"></div>
          </div>

          <div class="col-md-6">
            <label for="diagType" class="form-label" data-i18n="diagnostics_type">Type</label>
            <input type="text" class="form-control" id="diagType" name="type" required>
            <div class="invalid-feedback" id="diagTypeError"></div>
          </div>

          <div class="col-md-6">
            <label for="diagUnit" class="form-label">Unit</label>
            <input type="text" class="form-control" id="diagUnit" name="unit">
          </div>

          <div class="col-md-6">
            <label for="diagRoom" class="form-label">Room</label>
            <input type="text" class="form-control" id="diagRoom" name="room">
          </div>

          <div class="col-md-6">
            <label for="diagDate" class="form-label" data-i18n="diagnostics_date">Date</label>
            <input type="date" class="form-control" id="diagDate" name="date" required>
            <div class="invalid-feedback" id="diagDateError"></div>
          </div>

          <div class="col-md-6">
            <label for="diagTime" class="form-label">Time</label>
            <input type="time" class="form-control" id="diagTime" name="time">
          </div>

          <div class="col-md-6">
            <label for="diagExpediente" class="form-label">Expediente No.</label>
            <input type="text" class="form-control" id="diagExpediente" name="expediente_no">
          </div>

          <div class="col-md-6">
            <label for="diagCedula" class="form-label">Cédula</label>
            <input type="text" class="form-control" id="diagCedula" name="cedula">
          </div>

          <div class="col-md-6">
            <label for="diagInss" class="form-label">INSS</label>
            <input type="text" class="form-control" id="diagInss" name="inss_no">
          </div>

          <div class="col-md-6">
            <label for="diagPlan" class="form-label">Plan</label>
            <input type="text" class="form-control" id="diagPlan" name="plan">
          </div>

          <div class="col-md-6">
            <label for="diagWeight" class="form-label">Weight (kg)</label>
            <input type="number" step="0.1" class="form-control" id="diagWeight" name="weight">
          </div>

          <div class="col-md-6">
            <label for="diagHeight" class="form-label">Height (cm)</label>
            <input type="number" step="0.1" class="form-control" id="diagHeight" name="height">
          </div>

          <div class="col-md-6">
            <label for="diagAge" class="form-label">Age</label>
            <input type="number" class="form-control" id="diagAge" name="age">
          </div>

          <div class="col-md-6">
            <label for="diagSex" class="form-label">Sex</label>
            <select class="form-select" id="diagSex" name="sex">
              <option value="" selected>--</option>
              <option value="M">Male</option>
              <option value="F">Female</option>
              <option value="O">Other</option>
            </select>
          </div>

          <div class="col-12">
            <label for="diagDescription" class="form-label" data-i18n="diagnostics_description">Description</label>
            <textarea class="form-control" id="diagDescription" name="description" rows="4"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
        <button id="btnSaveDiag" class="btn btn-primary" type="button" data-i18n="save"><i class="fa-solid fa-save me-1"></i>Save</button>
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
  const form = document.getElementById('diagForm');
  const alertBox = document.getElementById('diagAlert');
  let modal = null;
  let diagnosticsTable = null;

  function initModal(){
    if (!modal) {
      const modalElement = document.getElementById('diagnosticModal');
      if (modalElement) {
        modal = new bootstrap.Modal(modalElement);
      }
    }
    return modal;
  }

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function setError(msg){
    if (!alertBox) return;
    if (!msg){
      alertBox.classList.add('d-none');
      alertBox.textContent = '';
      return;
    }
    alertBox.classList.remove('d-none');
    alertBox.textContent = msg;
  }

  function setFieldError(id, msg){
    const input = document.getElementById(id);
    const error = document.getElementById(id + 'Error');
    if (!input || !error) return;
    if (!msg){
      input.classList.remove('is-invalid');
      error.textContent = '';
      return;
    }
    input.classList.add('is-invalid');
    error.textContent = msg;
  }

  function clearErrors(){
    setFieldError('diagPatient', '');
    setFieldError('diagType', '');
    setFieldError('diagDate', '');
    setError('');
  }

  function resetForm(){
    form.reset();
    document.getElementById('diagId').value = '';
    clearErrors();
    document.getElementById('diagnosticModalTitle').setAttribute('data-i18n', 'diagnostics_add_btn');
    document.getElementById('diagnosticModalTitle').textContent = t('diagnostics_add_btn') || 'Add Diagnostic';
  }

  async function loadDiagnostic(id){
    try {
      const res = await fetch('/api/diagnostic_get.php?id=' + encodeURIComponent(id), { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){ setError(json.error || t('error')||'Error'); return; }
      const d = json.diagnostic || {};
      document.getElementById('diagId').value = d.id || '';
      document.getElementById('diagPatient').value = d.patient_id || '';
      document.getElementById('diagType').value = d.type || '';
      document.getElementById('diagUnit').value = d.unit || '';
      document.getElementById('diagRoom').value = d.room || '';
      document.getElementById('diagDate').value = d.date || '';
      document.getElementById('diagTime').value = d.time || '';
      document.getElementById('diagExpediente').value = d.expediente_no || '';
      document.getElementById('diagCedula').value = d.cedula || '';
      document.getElementById('diagInss').value = d.inss_no || '';
      document.getElementById('diagPlan').value = d.plan || '';
      document.getElementById('diagWeight').value = d.weight || '';
      document.getElementById('diagHeight').value = d.height || '';
      document.getElementById('diagAge').value = d.age || '';
      document.getElementById('diagSex').value = d.sex || '';
      document.getElementById('diagDescription').value = d.description || '';
      document.getElementById('diagnosticModalTitle').setAttribute('data-i18n', 'edit');
      document.getElementById('diagnosticModalTitle').textContent = t('edit') || 'Edit';
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

  async function loadModalPatients(){
    try {
      const res = await fetch('/api/patients_list.php?encountered=1', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return [];
      const patients = Array.isArray(json.data) ? json.data : [];
      const select = document.getElementById('diagPatient');
      select.innerHTML = `<option value="">${t('select_patient')||'Select patient'}</option>` +
        patients.map(p => {
          const name = `${p.first_name} ${p.last_name}`.trim();
          return `<option value="${encodeURIComponent(p.id)}">${escapeHtml(name)}</option>`;
        }).join('');
      return patients;
    } catch (e) {
      return [];
    }
  }

  async function submitForm(e){
    if (e && e.preventDefault) e.preventDefault();
    clearErrors();

    const pid = document.getElementById('diagPatient').value;
    const type = document.getElementById('diagType').value.trim();
    const date = document.getElementById('diagDate').value;

    if (!pid) setFieldError('diagPatient', t('patient') + ' is required');
    if (!type) setFieldError('diagType', t('diagnostics_type') + ' is required');
    if (!date) setFieldError('diagDate', t('diagnostics_date') + ' is required');

    if (!pid || !type || !date){
      setError(t('fix_errors') || 'Please fix errors');
      return;
    }

    const payload = {
      id: document.getElementById('diagId').value ? Number(document.getElementById('diagId').value) : undefined,
      patient_id: Number(pid),
      type,
      unit: document.getElementById('diagUnit').value.trim(),
      room: document.getElementById('diagRoom').value.trim(),
      date,
      time: document.getElementById('diagTime').value,
      expediente_no: document.getElementById('diagExpediente').value.trim(),
      cedula: document.getElementById('diagCedula').value.trim(),
      inss_no: document.getElementById('diagInss').value.trim(),
      plan: document.getElementById('diagPlan').value.trim(),
      weight: document.getElementById('diagWeight').value,
      height: document.getElementById('diagHeight').value,
      age: document.getElementById('diagAge').value,
      sex: document.getElementById('diagSex').value,
      description: document.getElementById('diagDescription').value.trim(),
    };

    const url = payload.id ? '/api/diagnostics_update.php' : '/api/diagnostics_create.php';

    try {
      const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!json.success) {
        setError(json.error || t('error') || 'Error');
        return;
      }
      initModal().hide();
      resetForm();
      loadDiagnostics();
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

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
      if (id) {
        resetForm();
        loadModalPatients().then(() => loadDiagnostic(id));
        const m = initModal();
        if (m) m.show();
      }
    }
  });

  btnAdd.addEventListener('click', ()=>{
    resetForm();
    loadModalPatients();
    const m = initModal();
    if (m) m.show();
  });

  form.addEventListener('submit', submitForm);

  document.getElementById('btnSaveDiag').addEventListener('click', submitForm);

  filter.addEventListener('change', loadDiagnostics);

  document.addEventListener('DOMContentLoaded', () => {
    initModal();
    loadPatients().then(()=> loadDiagnostics());
  });

  // If DOM is already loaded, initialize
  if (document.readyState === 'loading') {
    // Still loading
  } else {
    initModal();
    loadPatients().then(()=> loadDiagnostics());
  }
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
