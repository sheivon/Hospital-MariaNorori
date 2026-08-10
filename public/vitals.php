<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4" id="vitalsPage">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">
      <i class="fa-solid fa-heart-pulse me-2"></i>
      <span data-i18n="vitals_title">Vitals</span>
    </h2>
    <div class="d-flex align-items-center gap-2">
      <input type="search" id="vitalsSearch" class="form-control form-control-sm" placeholder="Search…" style="min-width:200px">
      <button id="btnVitalsAdd" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#vitalModal">
        <i class="fa-solid fa-plus me-1"></i><span data-i18n="vitals_add">Add Vitals</span>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" id="vitalsTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="vitals_patient">Patient</th>
              <th data-i18n="vitals_measured_at">Measured at</th>
              <th data-i18n="vitals_temperature">Temp (°C)</th>
              <th data-i18n="vitals_bp">BP</th>
              <th data-i18n="vitals_heart_rate">HR</th>
              <th data-i18n="vitals_respiratory_rate">RR</th>
              <th data-i18n="vitals_oxygen_saturation">SpO₂</th>
              <th data-i18n="vitals_weight">Weight (kg)</th>
              <th data-i18n="vitals_height">Height (cm)</th>
              <th data-i18n="vitals_bmi">BMI</th>
              <th data-i18n="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Vitals Modal -->
<div class="modal fade" id="vitalModal" tabindex="-1" aria-labelledby="vitalModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vitalModalTitle" data-i18n="vitals_add">Add Vitals</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="vitalAlert" class="alert alert-danger d-none" role="alert"></div>
        <form id="vitalForm" class="row g-3">
          <input type="hidden" id="vitalId" name="id">

          <div class="col-md-6">
            <label for="vitalPatient" class="form-label" data-i18n="vitals_patient">Patient</label>
            <select class="form-select" id="vitalPatient" name="patient_id" required>
              <option value="" data-i18n="select_patient">Select patient</option>
            </select>
            <div class="invalid-feedback" id="vitalPatientError"></div>
          </div>

          <div class="col-md-6">
            <label for="vitalEncounter" class="form-label" data-i18n="encounter">Encounter</label>
            <select class="form-select" id="vitalEncounter" name="encounter_id">
              <option value="" data-i18n="select_encounter">-- None --</option>
            </select>
          </div>

          <div class="col-md-4">
            <label for="vitalMeasuredAt" class="form-label" data-i18n="vitals_measured_at">Measured at</label>
            <input type="datetime-local" class="form-control" id="vitalMeasuredAt" name="measured_at">
          </div>

          <div class="col-md-4">
            <label for="vitalTemp" class="form-label" data-i18n="vitals_temperature">Temperature (°C)</label>
            <input type="number" step="0.1" min="25" max="45" class="form-control" id="vitalTemp" name="temperature_c" placeholder="36.5">
          </div>

          <div class="col-md-2">
            <label for="vitalSystolic" class="form-label" data-i18n="vitals_systolic">Systolic</label>
            <input type="number" min="40" max="260" class="form-control" id="vitalSystolic" name="systolic_bp" placeholder="120">
          </div>

          <div class="col-md-2">
            <label for="vitalDiastolic" class="form-label" data-i18n="vitals_diastolic">Diastolic</label>
            <input type="number" min="30" max="200" class="form-control" id="vitalDiastolic" name="diastolic_bp" placeholder="80">
          </div>

          <div class="col-md-3">
            <label for="vitalHR" class="form-label" data-i18n="vitals_heart_rate">Heart rate (bpm)</label>
            <input type="number" min="20" max="250" class="form-control" id="vitalHR" name="heart_rate" placeholder="72">
          </div>

          <div class="col-md-3">
            <label for="vitalRR" class="form-label" data-i18n="vitals_respiratory_rate">Respiratory rate</label>
            <input type="number" min="5" max="80" class="form-control" id="vitalRR" name="respiratory_rate" placeholder="16">
          </div>

          <div class="col-md-3">
            <label for="vitalSpO2" class="form-label" data-i18n="vitals_oxygen_saturation">SpO₂ (%)</label>
            <input type="number" step="0.01" min="50" max="100" class="form-control" id="vitalSpO2" name="oxygen_saturation" placeholder="98">
          </div>

          <div class="col-md-3">
            <label for="vitalWeight" class="form-label" data-i18n="vitals_weight">Weight (kg)</label>
            <input type="number" step="0.01" min="0" max="500" class="form-control" id="vitalWeight" name="weight_kg" placeholder="70">
          </div>

          <div class="col-md-3">
            <label for="vitalHeight" class="form-label" data-i18n="vitals_height">Height (cm)</label>
            <input type="number" step="0.01" min="0" max="260" class="form-control" id="vitalHeight" name="height_cm" placeholder="170">
          </div>

          <div class="col-md-3">
            <label for="vitalBmi" class="form-label" data-i18n="vitals_bmi">BMI</label>
            <input type="number" step="0.01" min="0" max="80" class="form-control" id="vitalBmi" name="bmi" placeholder="auto" readonly>
          </div>

          <div class="col-12">
            <label for="vitalNotes" class="form-label" data-i18n="notes">Notes</label>
            <textarea class="form-control" id="vitalNotes" name="notes" rows="2" maxlength="1000"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
        <button id="btnSaveVital" class="btn btn-primary" type="button">
          <i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const tblBody = document.querySelector('#vitalsTable tbody');
  const form = document.getElementById('vitalForm');
  const alertBox = document.getElementById('vitalAlert');
  const modalEl = document.getElementById('vitalModal');
  const searchInput = document.getElementById('vitalsSearch');
  const patientSelect = document.getElementById('vitalPatient');
  const encounterSelect = document.getElementById('vitalEncounter');
  const weightInput = document.getElementById('vitalWeight');
  const heightInput = document.getElementById('vitalHeight');
  const bmiInput = document.getElementById('vitalBmi');
  let dataTable = null;
  let allRows = [];
  let patientsData = [];
  let encountersData = [];

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function setAlert(msg){
    if (!alertBox) return;
    if (!msg){ alertBox.classList.add('d-none'); alertBox.textContent = ''; return; }
    alertBox.classList.remove('d-none');
    alertBox.textContent = msg;
  }

  function setFieldError(id, msg){
    const input = document.getElementById(id);
    const error = document.getElementById(id + 'Error');
    if (!input || !error) return;
    if (!msg){ input.classList.remove('is-invalid'); error.textContent = ''; return; }
    input.classList.add('is-invalid');
    error.textContent = msg;
  }

  function patientName(p){
    if (!p) return '';
    return `${p.first_name || ''} ${p.last_name || ''}`.trim();
  }

  function findPatientName(id){
    if (!id) return '';
    const p = patientsData.find(x => String(x.id) === String(id));
    return p ? patientName(p) : '';
  }

  function nowLocalInputValue(){
    const d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
  }

  function localInputToDb(value){
    if (!value) return '';
    const d = new Date(value);
    if (isNaN(d.getTime())) return '';
    const pad = n => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  }

  function dbToLocalInput(value){
    if (!value) return '';
    const m = String(value).match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::\d{2})?$/);
    if (!m) return '';
    return `${m[1]}-${m[2]}-${m[3]}T${m[4]}:${m[5]}`;
  }

  function recalcBmi(){
    const w = parseFloat(weightInput.value);
    const h = parseFloat(heightInput.value);
    if (!isNaN(w) && !isNaN(h) && w > 0 && h > 0){
      const meters = h / 100;
      const bmi = w / (meters * meters);
      bmiInput.value = bmi.toFixed(2);
    } else {
      bmiInput.value = '';
    }
  }

  function resetForm(){
    if (!form) return;
    form.reset();
    document.getElementById('vitalId').value = '';
    setFieldError('vitalPatient', '');
    setAlert('');
    bmiInput.value = '';
    if (patientSelect){
      patientSelect.innerHTML = `<option value="" data-i18n="select_patient">${escapeHtml(t('select_patient') || 'Select patient')}</option>` +
        patientsData.map(p => `<option value="${encodeURIComponent(p.id)}">${escapeHtml(patientName(p) || ('#' + p.id))}</option>`).join('');
    }
    if (encounterSelect){
      encounterSelect.innerHTML = `<option value="" data-i18n="select_encounter">${escapeHtml(t('select_encounter') || '-- None --')}</option>` +
        encountersData.map(e => {
          const when = e.encounter_date || '';
          return `<option value="${encodeURIComponent(e.id)}">#${e.id} · ${escapeHtml(when)} · ${escapeHtml(e.encounter_type || '')}</option>`;
        }).join('');
    }
    const measuredAt = document.getElementById('vitalMeasuredAt');
    if (measuredAt) measuredAt.value = nowLocalInputValue();
    const title = document.getElementById('vitalModalTitle');
    if (title) {
      title.setAttribute('data-i18n', 'vitals_add');
      title.textContent = t('vitals_add') || 'Add Vitals';
    }
  }

  function renderRows(rows){
    if (!tblBody) return;
    if (!rows.length){
      tblBody.innerHTML = `<tr><td colspan="12" class="text-center text-muted">${escapeHtml(t('vitals_empty') || 'No vitals recorded yet')}</td></tr>`;
      return;
    }
    tblBody.innerHTML = rows.map((r, i) => `
      <tr>
        <td>${i + 1}</td>
        <td>${escapeHtml(findPatientName(r.patient_id) || ('#' + (r.patient_id || '')))}</td>
        <td>${escapeHtml((r.measured_at || '').toString().replace(' ', ' ').slice(0, 16) || '')}</td>
        <td>${escapeHtml(r.temperature_c == null ? '' : String(r.temperature_c))}</td>
        <td>${escapeHtml(
          (r.systolic_bp == null ? '' : r.systolic_bp) +
          (r.systolic_bp != null || r.diastolic_bp != null ? '/' : '') +
          (r.diastolic_bp == null ? '' : r.diastolic_bp)
        )}</td>
        <td>${escapeHtml(r.heart_rate == null ? '' : String(r.heart_rate))}</td>
        <td>${escapeHtml(r.respiratory_rate == null ? '' : String(r.respiratory_rate))}</td>
        <td>${escapeHtml(r.oxygen_saturation == null ? '' : String(r.oxygen_saturation))}</td>
        <td>${escapeHtml(r.weight_kg == null ? '' : String(r.weight_kg))}</td>
        <td>${escapeHtml(r.height_cm == null ? '' : String(r.height_cm))}</td>
        <td>${escapeHtml(r.bmi == null ? '' : String(r.bmi))}</td>
        <td class="text-center">
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${r.id}" title="${escapeHtml(t('edit') || 'Edit')}">
              <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${escapeHtml(t('edit') || 'Edit')}</span>
            </button>
            <button class="btn btn-sm btn-danger btn-del table-action-btn" data-id="${r.id}" title="${escapeHtml(t('delete') || 'Delete')}">
              <i class="fa-solid fa-trash"></i><span class="btn-label">${escapeHtml(t('delete') || 'Delete')}</span>
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  function initDataTable(){
    if (typeof $ === 'undefined' || !$.fn.dataTable) {
      window.addEventListener('load', initDataTable, { once: true });
      return;
    }
    if ($.fn.dataTable.isDataTable('#vitalsTable')) {
      $('#vitalsTable').DataTable().destroy();
    }
    dataTable = $('#vitalsTable').DataTable({
      responsive: true,
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [[2, 'desc']],
      columnDefs: [
        { orderable: false, searchable: false, targets: [0, 11] }
      ]
    });
  }

  // Refresh the DataTable in place after CRUD — avoids touching innerHTML
  // on a DataTable-managed <tbody> (which loses the new rows).
  function refreshDataTable(){
    if (dataTable) {
      dataTable.clear();
      dataTable.rows.add($(tblBody).find('tr').detach());
      dataTable.draw();
      return;
    }
    initDataTable();
  }

  async function loadPatients(){
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return;
      patientsData = Array.isArray(json.data) ? json.data : [];
      if (patientSelect){
        patientSelect.innerHTML = `<option value="" data-i18n="select_patient">${escapeHtml(t('select_patient') || 'Select patient')}</option>` +
          patientsData.map(p => `<option value="${encodeURIComponent(p.id)}">${escapeHtml(patientName(p) || ('#' + p.id))}</option>`).join('');
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function loadEncounters(){
    try {
      const res = await fetch('/api/encounters_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return;
      encountersData = Array.isArray(json.data) ? json.data : [];
      if (encounterSelect){
        encounterSelect.innerHTML = `<option value="" data-i18n="select_encounter">${escapeHtml(t('select_encounter') || '-- None --')}</option>` +
          encountersData.map(e => {
            const when = e.encounter_date || '';
            return `<option value="${encodeURIComponent(e.id)}">#${e.id} · ${escapeHtml(when)} · ${escapeHtml(e.encounter_type || '')}</option>`;
          }).join('');
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function loadVitals(){
    if (!tblBody) return;
    tblBody.innerHTML = `<tr><td colspan="12" class="text-center">${escapeHtml(t('loading') || 'Loading...')}</td></tr>`;
    try {
      const res = await fetch('/api/vitals_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){
        tblBody.innerHTML = `<tr><td colspan="12" class="text-center text-danger">${escapeHtml(json.error || t('error') || 'Error')}</td></tr>`;
        if (dataTable) { dataTable.clear(); dataTable.draw(); }
        return;
      }
      allRows = Array.isArray(json.rows) ? json.rows : [];
      renderRows(allRows);
      if (dataTable) {
        refreshDataTable();
      } else {
        initDataTable();
      }
    } catch (e) {
      tblBody.innerHTML = `<tr><td colspan="12" class="text-center text-danger">${escapeHtml(t('error') || 'Error')}</td></tr>`;
      if (dataTable) { dataTable.clear(); dataTable.draw(); }
    }
  }

  function findRowById(id){
    return allRows.find(r => String(r.id) === String(id)) || null;
  }

  function openEditModal(id){
    const row = findRowById(id);
    if (!row) return;
    resetForm();
    document.getElementById('vitalId').value = row.id || '';
    document.getElementById('vitalPatient').value = row.patient_id ? encodeURIComponent(row.patient_id) : '';
    document.getElementById('vitalEncounter').value = row.encounter_id ? encodeURIComponent(row.encounter_id) : '';
    document.getElementById('vitalMeasuredAt').value = dbToLocalInput(row.measured_at);
    document.getElementById('vitalTemp').value = row.temperature_c == null ? '' : row.temperature_c;
    document.getElementById('vitalSystolic').value = row.systolic_bp == null ? '' : row.systolic_bp;
    document.getElementById('vitalDiastolic').value = row.diastolic_bp == null ? '' : row.diastolic_bp;
    document.getElementById('vitalHR').value = row.heart_rate == null ? '' : row.heart_rate;
    document.getElementById('vitalRR').value = row.respiratory_rate == null ? '' : row.respiratory_rate;
    document.getElementById('vitalSpO2').value = row.oxygen_saturation == null ? '' : row.oxygen_saturation;
    document.getElementById('vitalWeight').value = row.weight_kg == null ? '' : row.weight_kg;
    document.getElementById('vitalHeight').value = row.height_cm == null ? '' : row.height_cm;
    document.getElementById('vitalNotes').value = row.notes || '';
    recalcBmi();
    const title = document.getElementById('vitalModalTitle');
    if (title) {
      title.setAttribute('data-i18n', 'vitals_edit');
      title.textContent = t('vitals_edit') || 'Edit Vitals';
    }
    if (modalEl && window.bootstrap) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
  }

  function decodeSelectValue(v){
    if (!v) return '';
    try { return decodeURIComponent(v); } catch (e) { return v; }
  }

  function buildPayload(){
    const payload = {
      patient_id: decodeSelectValue(document.getElementById('vitalPatient').value),
      encounter_id: decodeSelectValue(document.getElementById('vitalEncounter').value),
      measured_at: localInputToDb(document.getElementById('vitalMeasuredAt').value || ''),
      temperature_c: document.getElementById('vitalTemp').value.trim(),
      systolic_bp: document.getElementById('vitalSystolic').value.trim(),
      diastolic_bp: document.getElementById('vitalDiastolic').value.trim(),
      heart_rate: document.getElementById('vitalHR').value.trim(),
      respiratory_rate: document.getElementById('vitalRR').value.trim(),
      oxygen_saturation: document.getElementById('vitalSpO2').value.trim(),
      weight_kg: document.getElementById('vitalWeight').value.trim(),
      height_cm: document.getElementById('vitalHeight').value.trim(),
      bmi: document.getElementById('vitalBmi').value.trim(),
      notes: document.getElementById('vitalNotes').value.trim(),
    };
    return payload;
  }

  async function submitForm(){
    setFieldError('vitalPatient', '');
    setAlert('');

    const patientId = document.getElementById('vitalPatient').value;
    if (!patientId) {
      setFieldError('vitalPatient', t('patient_required') || 'Please select a patient');
      setAlert(t('patient_required') || 'Please select a patient');
      return;
    }

    const id = document.getElementById('vitalId').value;
    const payload = buildPayload();
    if (id) payload.id = Number(id);

    const url = id ? '/api/vitals_update.php' : '/api/vitals_create.php';

    try {
      const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!json.success) {
        setAlert(json.error || t('error') || 'Error');
        return;
      }
      if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
      }
      resetForm();
      swal({ title: '', text: t('vitals_saved') || 'Vitals saved', icon: 'success' });
      loadVitals();
    } catch (e) {
      setAlert(t('error') || 'Error');
    }
  }

  async function deleteVital(id){
    try {
      const ok = await swal({
        title: t('delete_confirm') || 'Delete?',
        icon: 'warning',
        buttons: [t('cancel') || 'Cancel', t('confirm_yes') || 'Yes'],
        dangerMode: true
      });
      if (!ok) return;
      const res = await fetch('/api/vitals_delete.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(id) })
      });
      const json = await res.json();
      if (!json.success) {
        swal({ title: '', text: json.error || t('error') || 'Error', icon: 'error' });
        return;
      }
      swal({ title: '', text: t('vitals_deleted') || 'Vitals deleted', icon: 'success' });
      loadVitals();
    } catch (e) {
      swal({ title: '', text: e.message || t('error') || 'Error', icon: 'error' });
    }
  }

  // Wire up events
  if (tblBody) {
    tblBody.addEventListener('click', (e) => {
      const editBtn = e.target.closest('button.btn-edit');
      if (editBtn) {
        const id = editBtn.dataset.id;
        if (id) openEditModal(id);
        return;
      }
      const delBtn = e.target.closest('button.btn-del');
      if (delBtn) {
        const id = delBtn.dataset.id;
        if (id) deleteVital(id);
      }
    });
  }

  const btnAdd = document.getElementById('btnVitalsAdd');
  if (btnAdd) {
    btnAdd.addEventListener('click', () => {
      Promise.all([loadPatients(), loadEncounters()]).then(resetForm);
    });
  }

  const btnSave = document.getElementById('btnSaveVital');
  if (btnSave) {
    btnSave.addEventListener('click', submitForm);
  }

  if (weightInput) weightInput.addEventListener('input', recalcBmi);
  if (heightInput) heightInput.addEventListener('input', recalcBmi);

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      if (dataTable) dataTable.search(searchInput.value).draw();
    });
  }

  // Bootstrap modal backdrop cleanup (matches Appointments.js pattern)
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', () => {
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      if (document.body.classList.contains('modal-open')) {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
      }
    });
  }

  // Initial load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      Promise.all([loadPatients(), loadEncounters()]).then(loadVitals);
    });
  } else {
    Promise.all([loadPatients(), loadEncounters()]).then(loadVitals);
  }
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
