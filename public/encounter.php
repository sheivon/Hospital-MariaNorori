<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 id="pageTitle" data-i18n="encounters">Encounters</h2>
    <a href="/encounters.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
  </div>

  <div id="encAlert" class="alert alert-danger d-none" role="alert"></div>

  <form id="encForm" class="row g-3">
    <input type="hidden" id="encId" name="id"> 
    <div class="row mb-3 col-12"> 
        <div class="input-group">  
          <div class="label" data-i18n="expediente_no">Expediente No.</div> 
          <input type="text" id="encExpediente" class="form-control" readonly> 
        </div> 
    </div> 

    <div class="col-4 mb-3"> 
      <div class="input-group">  
        <div class="label" data-i18n="last_name">Apellido</div> 
        <input type="text" id="encPatientLastName" class="form-control" readonly> 
      </div> 
    </div> 

    <div class="col-4 mb-3"> 
      <div class="input-group">  
        <div class="label" data-i18n="first_name">Nombre</div> 
        <input type="text" id="encPatientFirstName" class="form-control" readonly> 
      </div> 
    </div> 

    <div class="mb-3 col-4"> 
      <div class="input-group">  
        <div class="label" data-i18n="patient">Patient</div> 
        <input type="text" id="encPatientFullName" class="form-control" readonly> 
      </div> 
    </div>

  <div class="mb-3 col-4 row">
      <div class="input-group">  
            <div class="label" data-i18n="encounters_NINNS">N° INNS:</div> 
            <input type="text" id="encNINNS" class="form-control" readonly> 
        </div>
  </div>


  <div>
    <div class="row mb-3">  
        <div class="col-md-4">
          <label for="encPatient" class="form-label" data-i18n="patient">Patient</label>
          <select id="encPatient" name="patient_id" class="form-select" required></select>
          <div class="invalid-feedback" id="encPatientError"></div>
        </div> 
        <div class="col-md-4">
          <label for="encDate" class="form-label" data-i18n="encounters_date">Date</label>
          <input type="datetime-local" class="form-control" id="encDate" name="encounter_date" required>
          <div class="invalid-feedback" id="encDateError"></div>
        </div>
      </div>
    </div>
 
    <div class="col-md-4">
      <label for="encType" class="form-label" data-i18n="encounters_type">Type</label>
      <select class="form-select" id="encType" name="encounter_type" required>
        <option value="" data-i18n="select_type">Select type</option>
        <option value="outpatient">Outpatient</option>
        <option value="inpatient">Inpatient</option>
        <option value="emergency">Emergency</option>
      </select>
      <div class="invalid-feedback" id="encTypeError"></div>
    </div>

    <div class="col-md-4">
      <label for="encTriage" class="form-label" data-i18n="triage_level">Triage Level</label>
      <select id="encTriage" name="triage_level" class="form-select">
        <option value="" data-i18n="select_triage">Select triage</option>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
        <option value="urgent">Urgent</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="encStatus" class="form-label" data-i18n="encounters_status">Status</label>
      <select id="encStatus" name="status" class="form-select">
        <option value="open" data-i18n="status_open">Open</option>
        <option value="closed" data-i18n="status_closed">Closed</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="encDoctor" class="form-label" data-i18n="encounters_doctor">Doctor</label>
      <select id="encDoctor" name="attending_user_id" class="form-select"></select>
    </div>

    <div class="col-md-6">
      <label for="encReason" class="form-label" data-i18n="encounters_reason">Reason</label>
      <textarea class="form-control" id="encReason" name="reason_for_visit" rows="3"></textarea>
    </div>

    <div class="col-12">
      <label for="encNotes" class="form-label" data-i18n="notes">Notes</label>
      <textarea class="form-control" id="encNotes" name="notes" rows="3"></textarea>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/encounters.php" class="btn btn-secondary" data-i18n="cancel">Cancel</a>
      <button id="btnSaveEnc" class="btn btn-primary" type="submit"><i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span></button>
    </div>
  </form>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const urlParams = new URLSearchParams(window.location.search);
  const encId = urlParams.get('id');
  const form = document.getElementById('encForm');
  const alertBox = document.getElementById('encAlert');
  let patientsData = [];

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
    setFieldError('encPatient', '');
    setFieldError('encDate', '');
    setFieldError('encType', '');
    setError('');
  }

  function updatePatientDisplay(patient){
    document.getElementById('encExpediente').value = patient?.expediente_no || '';
    document.getElementById('encPatientLastName').value = patient?.last_name || '';
    document.getElementById('encPatientFirstName').value = patient?.first_name || '';
    document.getElementById('encPatientFullName').value = `${patient?.first_name||''} ${patient?.last_name||''}`.trim();
  }

  function refreshSelectedPatientDetails(){
    const patientId = document.getElementById('encPatient').value;
    const patient = patientsData.find(p => String(p.id) === String(patientId));
    updatePatientDisplay(patient);
  }

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  async function loadPatients(){
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return [];
      patientsData = Array.isArray(json.data) ? json.data : [];
      const sel = document.getElementById('encPatient');
      sel.innerHTML = `<option value="">${t('select_patient')||'Select patient'}</option>` +
        patientsData.map(p => {
          const name = `${p.first_name} ${p.last_name}`.trim();
          return `<option value="${encodeURIComponent(p.id)}">${escapeHtml(name)}</option>`;
        }).join('');
      sel.addEventListener('change', refreshSelectedPatientDetails);
      return patientsData;
    } catch (e) {
      return [];
    }
  }

  async function loadDoctors(){
    try {
      const res = await fetch('/api/users_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return [];
      const users = Array.isArray(json.data) ? json.data : [];
      const sel = document.getElementById('encDoctor');
      sel.innerHTML = `<option value="">${t('select_doctor')||'Select doctor'}</option>` +
        users.map(u => `<option value="${encodeURIComponent(u.id)}">${escapeHtml(u.fullname||u.username||u.id.toString())}</option>`).join('');
      return users;
    } catch (e) {
      return [];
    }
  }

  async function loadEncounter(id){
    try {
      const res = await fetch('/api/encounters_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) { setError(json.error || t('error')||'Error'); return; }
      const enc = (json.data||[]).find(x=>String(x.id)===String(id));
      if (!enc) { setError(t('error')||'Error'); return; }
      document.getElementById('encId').value = enc.id || '';
      document.getElementById('encPatient').value = enc.patient_id || '';
      refreshSelectedPatientDetails();
      document.getElementById('encDate').value = enc.encounter_date ? enc.encounter_date.replace(' ', 'T') : '';
      document.getElementById('encType').value = enc.encounter_type || '';
      document.getElementById('encTriage').value = enc.triage_level || '';
      document.getElementById('encStatus').value = enc.status || 'open';
      document.getElementById('encDoctor').value = enc.attending_user_id || '';
      document.getElementById('encReason').value = enc.reason_for_visit || '';
      document.getElementById('encNotes').value = enc.notes || '';
      document.getElementById('pageTitle').textContent = t('edit') || 'Edit';
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

  async function submitForm(e){
    e.preventDefault();
    clearErrors();

    const patientId = document.getElementById('encPatient').value;
    const date = document.getElementById('encDate').value;
    const type = document.getElementById('encType').value.trim();

    if (!patientId) setFieldError('encPatient', t('patient') + ' is required');
    if (!date) setFieldError('encDate', t('encounters_date') + ' is required');
    if (!type) setFieldError('encType', t('encounters_type') + ' is required');

    if (!patientId || !date || !type){
      setError(t('fix_errors') || 'Please fix errors');
      return;
    }

    const payload = {
      id: document.getElementById('encId').value ? Number(document.getElementById('encId').value) : undefined,
      patient_id: Number(patientId),
      encounter_date: date,
      encounter_type: type,
      triage_level: document.getElementById('encTriage').value || null,
      status: document.getElementById('encStatus').value,
      attending_user_id: document.getElementById('encDoctor').value || null,
      reason_for_visit: document.getElementById('encReason').value.trim() || null,
      notes: document.getElementById('encNotes').value.trim() || null,
    };

    const url = payload.id ? '/api/encounters_update.php' : '/api/encounters_create.php';

    try {
      const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        body: new URLSearchParams(payload)
      });
      const json = await res.json();
      if (!json.success){ setError(json.error || t('error')||'Error'); return; }
      window.location.href = '/encounters.php';
    } catch (e) {
      setError(t('error')||'Error');
    }
  }

  form.addEventListener('submit', submitForm);

  Promise.all([loadPatients(), loadDoctors()]).then(()=>{
    if (encId) loadEncounter(encId);
  });
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
