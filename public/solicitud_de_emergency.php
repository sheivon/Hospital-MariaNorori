<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 data-i18n="emergency_form_title">Emergency Admissions / Discharges</h2>
      <p class="text-muted mb-0" data-i18n="emergency_form_description">Emergency intake form for hospital patients.</p>
    </div>
    <a href="/emergency.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
  </div>

  <div id="emergencyAlert" class="alert alert-danger d-none" role="alert"></div>

  <form id="emergencyForm" class="row g-3">
    <input type="hidden" id="patientId" name="patient_id">

    <div class="col-md-6">
      <label for="patientDisplay" class="form-label" data-i18n="patient">Patient</label>
      <div class="input-group">
        <input type="text" id="patientDisplay" class="form-control" readonly data-i18n-placeholder="select_patient" placeholder="Select patient">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#patientsListModal" data-i18n="search_patient">Search patient</button>
      </div>
    </div>
    <div class="col-md-3">
      <label for="patientCedula" class="form-label" data-i18n="cedula">Cédula</label>
      <input type="text" id="patientCedula" class="form-control" readonly>
    </div>
    <div class="col-md-3">
      <label for="patientExpediente" class="form-label" data-i18n="expediente_no">Expediente</label>
      <input type="text" id="patientExpediente" class="form-control" readonly>
    </div>

    <div class="col-12">
      <h5 data-i18n="emergency_basic_info">Basic information</h5>
    </div>
    <div class="col-md-4">
      <label for="sex" class="form-label" data-i18n="gender">Sex</label>
      <select id="sex" name="sex" class="form-select">
        <option value="" data-i18n="select_gender">Select</option>
        <option value="M" data-i18n="gender_male">M</option>
        <option value="F" data-i18n="gender_female">F</option>
      </select>
    </div>
    <div class="col-md-4">
      <label for="civilStatus" class="form-label" data-i18n="marital_status">Marital status</label>
      <select id="civilStatus" name="civil_status" class="form-select">
        <option value="" data-i18n="select_marital_status">Select</option>
        <option value="Soltero" data-i18n="marital_single">Single</option>
        <option value="Casado" data-i18n="marital_married">Married</option>
        <option value="Viudo" data-i18n="marital_widowed">Widowed</option>
        <option value="Divorciado" data-i18n="marital_divorced">Divorced</option>
      </select>
    </div>
    <div class="col-md-4">
      <label for="education" class="form-label" data-i18n="education_level">Education</label>
      <select id="education" name="education" class="form-select">
        <option value="" data-i18n="select_education">Select</option>
        <option value="Primaria" data-i18n="education_primary">Primary</option>
        <option value="Secundaria" data-i18n="education_secondary">Secondary</option>
        <option value="Universitaria" data-i18n="education_university">University</option>
        <option value="Otra" data-i18n="education_other">Other</option>
      </select>
    </div>

    <div class="col-md-4">
      <label for="patientCategory" class="form-label" data-i18n="emergency_field_patient_category">Patient category</label>
      <input type="text" id="patientCategory" name="patient_category" class="form-control" data-i18n-placeholder="emergency_placeholder_patient_category" placeholder="e.g. Active insured">
    </div>
    <div class="col-md-4">
      <label for="inssNumber" class="form-label" data-i18n="insurance_policy_no">INSS number</label>
      <input type="text" id="inssNumber" name="inss_number" class="form-control" data-i18n-placeholder="emergency_placeholder_inss_number" placeholder="INSS number">
    </div>
    <div class="col-md-4">
      <label for="address" class="form-label" data-i18n="address">Address</label>
      <input type="text" id="address" name="address" class="form-control" data-i18n-placeholder="emergency_placeholder_address" placeholder="Full patient address">
    </div>

    <div class="col-md-4">
      <label for="locality" class="form-label" data-i18n="emergency_field_locality">Locality</label>
      <input type="text" id="locality" name="locality" class="form-control" data-i18n-placeholder="emergency_placeholder_locality" placeholder="Locality / Municipality">
    </div>
    <div class="col-md-4">
      <label for="district" class="form-label" data-i18n="emergency_field_district">Municipality / District</label>
      <input type="text" id="district" name="district" class="form-control" data-i18n-placeholder="emergency_placeholder_district" placeholder="Municipality or District">
    </div>
    <div class="col-md-4">
      <label for="healthUnit" class="form-label" data-i18n="emergency_field_health_unit">Health unit / Neighborhood</label>
      <input type="text" id="healthUnit" name="health_unit" class="form-control" data-i18n-placeholder="emergency_placeholder_health_unit" placeholder="Health unit / Neighborhood">
    </div>

    <div class="col-12">
      <h5 data-i18n="emergency_admission_heading">Admission</h5>
    </div>
    <div class="col-md-3">
      <label for="admissionDate" class="form-label" data-i18n="emergency_field_admission_date">Admission date</label>
      <input type="date" id="admissionDate" name="admission_date" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="admissionTime" class="form-label" data-i18n="emergency_field_admission_time">Admission time</label>
      <input type="time" id="admissionTime" name="admission_time" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="admissionService" class="form-label" data-i18n="emergency_field_service">Service</label>
      <input type="text" id="admissionService" name="admission_service" class="form-control" data-i18n-placeholder="emergency_placeholder_service" placeholder="Service">
    </div>
    <div class="col-md-3">
      <label for="admissionDiagnosis" class="form-label" data-i18n="emergency_field_admission_diagnosis">Admission diagnosis</label>
      <input type="text" id="admissionDiagnosis" name="admission_diagnosis" class="form-control" data-i18n-placeholder="emergency_placeholder_admission_diagnosis" placeholder="Admission diagnosis">
    </div>

    <div class="col-md-4">
      <label class="form-label" data-i18n="emergency_field_admission_source">Admission source</label>
      <select id="admissionSource" name="admission_source" class="form-select">
        <option value="" data-i18n="select_option">Select</option>
        <option value="Consulta externa" data-i18n="emergency_option_outpatient">Outpatient consultation</option>
        <option value="Emergencia" data-i18n="encounter_type_emergency">Emergency</option>
        <option value="Referido" data-i18n="emergency_option_referred">Referred from another facility</option>
      </select>
    </div>
    <div class="col-md-4">
      <label for="orderingDoctor" class="form-label" data-i18n="emergency_field_ordering_doctor">Ordering doctor</label>
      <input type="text" id="orderingDoctor" name="ordering_doctor" class="form-control" data-i18n-placeholder="emergency_placeholder_ordering_doctor" placeholder="Doctor name and code">
    </div>
    <div class="col-md-4">
      <label for="admissionNumber" class="form-label" data-i18n="emergency_field_admission_number">Admission number</label>
      <input type="text" id="admissionNumber" name="admission_number" class="form-control" data-i18n-placeholder="emergency_placeholder_admission_number" placeholder="Admission number">
    </div>

    <div class="col-12">
      <h5 data-i18n="emergency_discharge_heading">Discharge</h5>
    </div>
    <div class="col-md-3">
      <label for="dischargeDate" class="form-label" data-i18n="emergency_field_discharge_date">Discharge date</label>
      <input type="date" id="dischargeDate" name="discharge_date" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="dischargeTime" class="form-label" data-i18n="emergency_field_discharge_time">Discharge time</label>
      <input type="time" id="dischargeTime" name="discharge_time" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="dischargeService" class="form-label" data-i18n="emergency_field_discharge_service">Discharge service</label>
      <input type="text" id="dischargeService" name="discharge_service" class="form-control" data-i18n-placeholder="emergency_placeholder_discharge_service" placeholder="Discharge service">
    </div>
    <div class="col-md-3">
      <label for="dischargeDiagnosis" class="form-label" data-i18n="emergency_field_discharge_diagnosis">Discharge diagnosis</label>
      <input type="text" id="dischargeDiagnosis" name="discharge_diagnosis" class="form-control" data-i18n-placeholder="emergency_placeholder_discharge_diagnosis" placeholder="Discharge diagnosis">
    </div>

    <div class="col-md-4">
      <label for="daysOfStay" class="form-label" data-i18n="emergency_field_days_of_stay">Days of stay</label>
      <input type="number" id="daysOfStay" name="days_of_stay" class="form-control" min="0" data-i18n-placeholder="emergency_placeholder_days_of_stay" placeholder="Days of stay">
    </div>
    <div class="col-md-4">
      <label for="principalDiagnosis" class="form-label" data-i18n="emergency_field_principal_diagnosis">Principal diagnosis</label>
      <input type="text" id="principalDiagnosis" name="principal_diagnosis" class="form-control" data-i18n-placeholder="emergency_placeholder_principal_diagnosis" placeholder="Principal diagnosis">
    </div>
    <div class="col-md-4">
      <label for="complementary" class="form-label" data-i18n="emergency_field_complementary">Complementary</label>
      <input type="text" id="complementary" name="complementary" class="form-control" data-i18n-placeholder="emergency_placeholder_complementary" placeholder="Complementary">
    </div>

    <div class="col-md-6">
      <label for="surgeries" class="form-label" data-i18n="emergency_field_surgeries">Surgeries performed</label>
      <textarea id="surgeries" name="surgeries" class="form-control" rows="2" data-i18n-placeholder="emergency_placeholder_surgeries" placeholder="Surgeries performed"></textarea>
    </div>
    <div class="col-md-3">
      <label class="form-label" data-i18n="emergency_field_work_accident">Work accident</label>
      <select id="workAccident" name="work_accident" class="form-select">
        <option value="" data-i18n="select_option">Select</option>
        <option value="Sí">Sí</option>
        <option value="No">No</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label" data-i18n="emergency_field_occupational_disease">Occupational disease</label>
      <select id="occupationalDisease" name="occupational_disease" class="form-select">
        <option value="" data-i18n="select_option">Select</option>
        <option value="Sí">Sí</option>
        <option value="No">No</option>
      </select>
    </div>

    <div class="col-12">
      <label for="traumaCause" class="form-label" data-i18n="emergency_field_trauma_cause">Trauma / cause</label>
      <input type="text" id="traumaCause" name="trauma_cause" class="form-control" data-i18n-placeholder="emergency_placeholder_trauma_cause" placeholder="Intentional, self-inflicted, traffic accident, other">
    </div>
    <div class="col-md-4">
      <label class="form-label" data-i18n="emergency_field_hospital_infection">Hospital-acquired infection</label>
      <select id="hospitalInfection" name="hospital_infection" class="form-select">
        <option value="" data-i18n="select_option">Select</option>
        <option value="Sí">Sí</option>
        <option value="No">No</option>
      </select>
    </div>
    <div class="col-md-8">
      <label for="infectionDetails" class="form-label" data-i18n="emergency_field_infection_details">Specify infection</label>
      <input type="text" id="infectionDetails" name="infection_details" class="form-control" data-i18n-placeholder="emergency_placeholder_infection_details" placeholder="Pneumonia, urinary tract, wound, other">
    </div>

    <div class="col-md-4">
      <label for="dischargeType" class="form-label" data-i18n="emergency_field_discharge_type">Discharge type</label>
      <select id="dischargeType" name="discharge_type" class="form-select">
        <option value="" data-i18n="select_option">Select</option>
        <option value="Alta" data-i18n="emergency_option_discharge_home">Discharge home</option>
        <option value="Defunción" data-i18n="emergency_option_deceased">Deceased</option>
        <option value="Abandono" data-i18n="emergency_option_abandonment">Abandonment</option>
        <option value="Fuga" data-i18n="emergency_option_escape">Escape</option>
        <option value="Referido" data-i18n="emergency_option_referred">Referred</option>
      </select>
    </div>
    <div class="col-md-8">
      <label for="treatingDoctor" class="form-label" data-i18n="emergency_field_treating_doctor">Doctor name, signature and stamp</label>
      <input type="text" id="treatingDoctor" name="treating_doctor" class="form-control" data-i18n-placeholder="emergency_placeholder_treating_doctor" placeholder="Doctor name and stamp">
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/emergency.php" class="btn btn-secondary" data-i18n="cancel">Cancel</a>
      <button id="submitEmergency" class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane me-1"></i><span data-i18n="save">Save</span></button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>

<script>
(function() {
  const t = window.i18n_t || (k => k);
  const alertBox = document.getElementById('emergencyAlert');
  const form = document.getElementById('emergencyForm');
  const patientDisplay = document.getElementById('patientDisplay');
  const patientCedula = document.getElementById('patientCedula');
  const patientExpediente = document.getElementById('patientExpediente');
  const patientIdInput = document.getElementById('patientId');
  const patientsModal = document.getElementById('patientsListModal');
  const patientsModalBody = document.querySelector('#patientsModalSelectionTable tbody');
  const patientsModalMessage = document.getElementById('patientsModalMessage');
  let patientModalInstance = null;
  let patientsData = [];

  function setAlert(message, type = 'danger') {
    if (!alertBox) return;
    if (!message) {
      alertBox.classList.add('d-none');
      alertBox.textContent = '';
      return;
    }
    alertBox.classList.remove('d-none');
    alertBox.classList.remove('alert-danger', 'alert-success', 'alert-warning');
    alertBox.classList.add('alert-' + type);
    alertBox.textContent = message;
  }

  function setModalMessage(message, type = 'info') {
    if (!patientsModalMessage) return;
    patientsModalMessage.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
    patientsModalMessage.classList.add('alert-' + type);
    patientsModalMessage.textContent = message;
  }

  function clearModalMessage() {
    if (!patientsModalMessage) return;
    patientsModalMessage.classList.add('d-none');
    patientsModalMessage.textContent = '';
  }

  function escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function formatPatientRow(patient) {
    const name = [patient.first_name, patient.last_name].filter(Boolean).join(' ');
    return `
      <tr>
        <td>${escapeHtml(patient.id)}</td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(patient.cedula || '')}</td>
        <td>${escapeHtml(patient.dob || '')}</td>
        <td>${escapeHtml(patient.email || '')}</td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-primary select-patient-btn table-action-btn" data-patient='${encodeURIComponent(JSON.stringify(patient))}' title="${t('select_patient') || 'Select'}">
          <i class="fa-solid fa-check"></i><span class="btn-label">${t('select_patient') || 'Select'}</span>
        </button></td>
      </tr>`;
  }

  function selectPatient(patient) {
    patientIdInput.value = patient.id;
    patientDisplay.value = [patient.first_name, patient.last_name].filter(Boolean).join(' ');
    patientCedula.value = patient.cedula || '';
    patientExpediente.value = patient.expediente_no || '';
    document.getElementById('sex').value = patient.gender || '';
    document.getElementById('civilStatus').value = patient.marital_status || '';
    document.getElementById('education').value = patient.education_level || '';
    document.getElementById('patientCategory').value = patient.procedencia || '';
    document.getElementById('inssNumber').value = patient.insurance_policy_no || '';
    document.getElementById('address').value = patient.address || '';
    document.getElementById('locality').value = patient.locality || patient.procedencia || '';
    document.getElementById('district').value = patient.district || '';
    document.getElementById('healthUnit').value = patient.health_unit || '';

    if (patientModalInstance) {
      patientModalInstance.hide();
    }
  }

  async function loadPatients() {
    patientsModalBody.innerHTML = `<tr><td colspan="6" class="text-center">${t('loading') || 'Loading...'}</td></tr>`;
    clearModalMessage();

    try {
      const res = await fetch('/api/patients_list.php?emergency_available=1', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        setModalMessage(t('error_loading_patients') || 'Unable to load patient list.', 'danger');
        patientsModalBody.innerHTML = `<tr><td colspan="6" class="text-center">${t('no_patients_available') || 'No patients available.'}</td></tr>`;
        return;
      }

      patientsData = json.data;
      patientsModalBody.innerHTML = patientsData.map(formatPatientRow).join('') || `<tr><td colspan="6" class="text-center">${t('no_patients_available') || 'No patients available.'}</td></tr>`;
      document.querySelectorAll('.select-patient-btn').forEach(button => {
        button.addEventListener('click', () => {
          const patientJson = decodeURIComponent(button.getAttribute('data-patient') || '');
          try {
            const patient = JSON.parse(patientJson);
            selectPatient(patient);
          } catch (err) {
            console.error(err);
          }
        });
      });
    } catch (error) {
      setModalMessage(t('error_loading_patients') || 'Error loading patients.', 'danger');
      patientsModalBody.innerHTML = `<tr><td colspan="6" class="text-center">${t('error_loading_patients') || 'Error loading patients.'}</td></tr>`;
    }
  }

  async function createEmergencyRecord(payload) {
    const res = await fetch('/api/emergency_create.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return res.json();
  }

  function buildPayload() {
    const formData = new FormData(form);
    const payload = {};
    formData.forEach((value, key) => {
      payload[key] = value;
    });
    return payload;
  }

  form.addEventListener('submit', async event => {
    event.preventDefault();
    setAlert('');

    const patientId = patientIdInput.value.trim();
    const admissionDate = document.getElementById('admissionDate').value;

    if (!patientId) {
      setAlert(t('emergency_select_patient') || 'Please select a patient before submitting.');
      return;
    }
    if (!admissionDate) {
      setAlert(t('emergency_enter_admission_date') || 'Please enter the admission date.');
      return;
    }

    const submitButton = document.getElementById('submitEmergency');
    submitButton.disabled = true;
    submitButton.textContent = 'Guardando...';

    try {
      const payload = buildPayload();
      const result = await createEmergencyRecord(payload);
      if (!result.success) {
        setAlert(result.error || (t('emergency_save_failed') || 'Unable to save the emergency request.'));
        return;
      }

      window.location.href = '/emergency.php';
      return;
    } catch (error) {
      console.error(error);
      setAlert(t('emergency_send_failed') || 'Error sending the request. Please try again.');
    } finally {
      submitButton.disabled = false;
      submitButton.innerHTML = `<i class="fa-solid fa-paper-plane me-1"></i>${t('save') || 'Save'}`;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    patientModalInstance = new bootstrap.Modal(patientsModal);
    patientsModal.addEventListener('show.bs.modal', loadPatients);
  });
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
