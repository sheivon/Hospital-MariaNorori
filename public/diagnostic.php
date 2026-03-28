<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 id="pageTitle" data-i18n="diagnostics_title">Diagnostics</h2>
    <a href="/diagnostics.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
  </div>

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

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/diagnostics.php" class="btn btn-secondary" data-i18n="cancel">Cancel</a>
      <button id="btnSaveDiag" class="btn btn-primary" type="submit"><i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span></button>
    </div>
  </form>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const urlParams = new URLSearchParams(window.location.search);
  const diagId = urlParams.get('id');
  const form = document.getElementById('diagForm');
  const alertBox = document.getElementById('diagAlert');

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

  async function loadPatients(){
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
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

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

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
      document.getElementById('pageTitle').textContent = t('edit') || 'Edit';
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

  async function submitForm(e){
    e.preventDefault();
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
      window.location.href = '/diagnostics.php';
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

  form.addEventListener('submit', submitForm);

  loadPatients().then(()=>{
    if (diagId) loadDiagnostic(diagId);
  });
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
