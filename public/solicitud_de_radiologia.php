<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>Solicitud de Examen Radiológico</h2>
      <p class="text-muted mb-0">Registre y almacene solicitudes de radiología por paciente.</p>
    </div>
    <a href="/radiologia.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Volver a radiología</a>
  </div>

  <div id="radiologyAlert" class="alert alert-danger d-none" role="alert"></div>

  <form id="radiologyForm" class="row g-3">
    <input type="hidden" id="patientId" name="patient_id">

    <div class="col-md-6">
      <label for="patientDisplay" class="form-label">Paciente</label>
      <div class="input-group">
        <input type="text" id="patientDisplay" class="form-control" readonly placeholder="Seleccione un paciente">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#patientsListModal">Buscar paciente</button>
      </div>
    </div>
    <div class="col-md-3">
      <label for="patientCedula" class="form-label">Cédula</label>
      <input type="text" id="patientCedula" class="form-control" readonly>
    </div>
    <div class="col-md-3">
      <label for="patientExpediente" class="form-label">Expediente</label>
      <input type="text" id="patientExpediente" class="form-control" readonly>
    </div>

    <div class="col-md-4">
      <label for="unit" class="form-label">Unidad</label>
      <input type="text" id="unit" name="unit" class="form-control" placeholder="Unidad" required>
    </div>
    <div class="col-md-4">
      <label for="firstLastName" class="form-label">1er Apellido</label>
      <input type="text" id="firstLastName" name="first_last_name" class="form-control" placeholder="1er Apellido" required>
    </div>
    <div class="col-md-4">
      <label for="secondLastName" class="form-label">2do Apellido</label>
      <input type="text" id="secondLastName" name="second_last_name" class="form-control" placeholder="2do Apellido">
    </div>

    <div class="col-md-6">
      <label for="names" class="form-label">Nombres</label>
      <input type="text" id="names" name="names" class="form-control" placeholder="Nombres" required>
    </div>
    <div class="col-md-6">
      <label for="age" class="form-label">Edad</label>
      <input type="text" id="age" name="age" class="form-control" placeholder="Edad" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Asegurado</label>
      <div class="d-flex gap-3 align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="insured" id="insuredYes" value="SI" checked>
          <label class="form-check-label" for="insuredYes">SI</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="insured" id="insuredNo" value="NO">
          <label class="form-check-label" for="insuredNo">NO</label>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <label class="form-label">Sexo</label>
      <div class="d-flex gap-3 align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="gender" id="genderM" value="M" checked>
          <label class="form-check-label" for="genderM">M</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="gender" id="genderF" value="F">
          <label class="form-check-label" for="genderF">F</label>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <label for="requestDate" class="form-label">Fecha de solicitud</label>
      <input type="date" id="requestDate" name="request_date" class="form-control" required>
    </div>

    <div class="col-md-4">
      <label for="clinicBed" class="form-label">Clínica o cama No.</label>
      <input type="text" id="clinicBed" name="clinic_bed" class="form-control" placeholder="Clínica o cama No.">
    </div>
    <div class="col-md-4">
      <label for="service" class="form-label">Servicio</label>
      <input type="text" id="service" name="service" class="form-control" placeholder="Servicio" required>
    </div>
    <div class="col-md-4">
      <label for="code" class="form-label">Código</label>
      <input type="text" id="code" name="code" class="form-control" placeholder="Código">
    </div>

    <div class="col-12">
      <h5>Solicitud de examen radiológico</h5>
    </div>
    <div class="col-md-4">
      <label for="priorRadiograph" class="form-label">Tiene radiografía anterior</label>
      <input type="text" id="priorRadiograph" name="prior_radiograph" class="form-control" placeholder="SI / NO">
    </div>
    <div class="col-md-4">
      <label for="priorRadiographCode" class="form-label">Código anterior</label>
      <input type="text" id="priorRadiographCode" name="prior_radiograph_code" class="form-control" placeholder="Código anterior">
    </div>
    <div class="col-md-4">
      <label for="examRequested" class="form-label">Examen solicitado</label>
      <input type="text" id="examRequested" name="exam_requested" class="form-control" placeholder="Ej. Radiografía de tórax" required>
    </div>

    <div class="col-12">
      <label for="clinicalData" class="form-label">Datos clínicos</label>
      <textarea id="clinicalData" name="clinical_data" rows="3" class="form-control" placeholder="Datos clínicos"></textarea>
    </div>
    <div class="col-md-6">
      <label for="evolutionTime" class="form-label">Tiempo de evolución</label>
      <input type="text" id="evolutionTime" name="evolution_time" class="form-control" placeholder="Tiempo de evolución">
    </div>
    <div class="col-md-6">
      <label for="presumptiveDiagnosis" class="form-label">Diagnóstico de presunción</label>
      <input type="text" id="presumptiveDiagnosis" name="presumptive_diagnosis" class="form-control" placeholder="Diagnóstico de presunción">
    </div>
    <div class="col-12">
      <label for="observations" class="form-label">Observaciones</label>
      <textarea id="observations" name="observations" rows="3" class="form-control" placeholder="Observaciones"></textarea>
    </div>

    <div class="col-md-6">
      <label for="doctorCode" class="form-label">Firma y código médico solicitante</label>
      <input type="text" id="doctorCode" name="doctor_code" class="form-control" placeholder="Firma y código médico">
    </div>
    <div class="col-md-6">
      <label for="technician" class="form-label">Técnico R.X.</label>
      <input type="text" id="technician" name="technician" class="form-control" placeholder="Técnico R.X.">
    </div>

    <div class="col-12">
      <h5>Para uso del servicio de radiología</h5>
    </div>
    <div class="col-md-3">
      <label for="platesUsed" class="form-label">Cantidad de placas usadas</label>
      <input type="text" id="platesUsed" name="plates_used" class="form-control" placeholder="Cantidad de placas">
    </div>
    <div class="col-md-3">
      <label for="radiologyDate" class="form-label">Fecha</label>
      <input type="date" id="radiologyDate" name="radiology_date" class="form-control">
    </div>
    <div class="col-md-3">
      <label for="radiographsArchived" class="form-label">Radiografías archivadas</label>
      <input type="text" id="radiographsArchived" name="radiographs_archived" class="form-control" placeholder="Radiografías archivadas">
    </div>
    <div class="col-md-3">
      <label for="radiographCount" class="form-label">Número de radiografías</label>
      <input type="text" id="radiographCount" name="radiograph_count" class="form-control" placeholder="Número de radiografías">
    </div>

    <div class="col-12">
      <label for="dictatingDoctorCode" class="form-label">Firma y código del médico que dictaminó</label>
      <input type="text" id="dictatingDoctorCode" name="dictating_doctor_code" class="form-control" placeholder="Firma y código del médico que dictaminó">
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/radiologia.php" class="btn btn-secondary">Cancelar</a>
      <button id="submitRadiology" class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane me-1"></i>Enviar solicitud</button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>

<script>
(function() {
  const alertBox = document.getElementById('radiologyAlert');
  const form = document.getElementById('radiologyForm');
  const requestDateInput = document.getElementById('requestDate');
  const patientDisplay = document.getElementById('patientDisplay');
  const patientCedula = document.getElementById('patientCedula');
  const patientExpediente = document.getElementById('patientExpediente');
  const patientIdInput = document.getElementById('patientId');
  const patientsModal = document.getElementById('patientsListModal');
  const patientsModalBody = document.querySelector('#patientsModalSelectionTable tbody');
  const patientsModalMessage = document.getElementById('patientsModalMessage');
  let patientModalInstance = null;

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
    const cedula = patient.cedula || '';
    const expediente = patient.expediente_no || '';
    return `
      <tr>
        <td>${escapeHtml(patient.id)}</td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(cedula)}</td>
        <td>${escapeHtml(patient.dob || '')}</td>
        <td>${escapeHtml(patient.email || '')}</td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-primary select-patient-btn table-action-btn" data-patient='${encodeURIComponent(JSON.stringify(patient))}' title="Seleccionar">
            <i class="fa-solid fa-check"></i><span class="btn-label">Seleccionar</span>
          </button>
        </td>
      </tr>`;
  }

  function selectPatient(patient) {
    patientIdInput.value = patient.id;
    patientDisplay.value = [patient.first_name, patient.last_name].filter(Boolean).join(' ');
    patientCedula.value = patient.cedula || '';
    patientExpediente.value = patient.expediente_no || '';

    document.getElementById('firstLastName').value = patient.last_name || '';
    document.getElementById('names').value = patient.first_name || '';

    if (patientModalInstance) {
      patientModalInstance.hide();
    }
  }

  async function loadPatients() {
    patientsModalBody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando...</td></tr>';
    clearModalMessage();

    try {
      const res = await fetch('/api/patients_list.php?encountered=1', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        setModalMessage('No se pudo cargar la lista de pacientes.', 'danger');
        patientsModalBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay pacientes disponibles.</td></tr>';
        return;
      }

      const rows = json.data.map(formatPatientRow).join('');
      patientsModalBody.innerHTML = rows || '<tr><td colspan="6" class="text-center">No hay pacientes registrados.</td></tr>';

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
      setModalMessage('Error al cargar pacientes.', 'danger');
      patientsModalBody.innerHTML = '<tr><td colspan="6" class="text-center">Error cargando pacientes.</td></tr>';
    }
  }

  async function createRadiologyRequest(payload) {
    const res = await fetch('/api/radiologia_create.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return res.json();
  }

  form.addEventListener('submit', async event => {
    event.preventDefault();
    setAlert('');

    const patientId = patientIdInput.value.trim();
    const requestDate = requestDateInput.value;
    const unit = document.getElementById('unit').value.trim();
    const firstLastName = document.getElementById('firstLastName').value.trim();
    const secondLastName = document.getElementById('secondLastName').value.trim();
    const names = document.getElementById('names').value.trim();
    const age = document.getElementById('age').value.trim();
    const service = document.getElementById('service').value.trim();
    const examRequested = document.getElementById('examRequested').value.trim();

    if (!patientId) {
      setAlert('Seleccione un paciente antes de enviar la solicitud.');
      return;
    }
    if (!requestDate) {
      setAlert('Ingrese la fecha de solicitud.');
      return;
    }
    if (!unit || !firstLastName || !names || !age || !service || !examRequested) {
      setAlert('Complete los campos obligatorios del formulario.');
      return;
    }

    const submitButton = document.getElementById('submitRadiology');
    submitButton.disabled = true;
    submitButton.textContent = 'Guardando...';

    const payload = {
      patient_id: Number(patientId),
      unit,
      first_last_name: firstLastName,
      second_last_name: secondLastName,
      names,
      insured: document.querySelector('input[name="insured"]:checked')?.value || '',
      gender: document.querySelector('input[name="gender"]:checked')?.value || '',
      age,
      request_date: requestDate,
      clinic_bed: document.getElementById('clinicBed').value.trim(),
      service,
      code: document.getElementById('code').value.trim(),
      prior_radiograph: document.getElementById('priorRadiograph').value.trim(),
      prior_radiograph_code: document.getElementById('priorRadiographCode').value.trim(),
      exam_requested: examRequested,
      clinical_data: document.getElementById('clinicalData').value.trim(),
      evolution_time: document.getElementById('evolutionTime').value.trim(),
      presumptive_diagnosis: document.getElementById('presumptiveDiagnosis').value.trim(),
      observations: document.getElementById('observations').value.trim(),
      doctor_code: document.getElementById('doctorCode').value.trim(),
      technician: document.getElementById('technician').value.trim(),
      plates_used: document.getElementById('platesUsed').value.trim(),
      findings: document.getElementById('findings').value.trim(),
      conclusions: document.getElementById('conclusions').value.trim(),
      radiology_date: document.getElementById('radiologyDate').value,
      radiographs_archived: document.getElementById('radiographsArchived').value.trim(),
      radiograph_count: document.getElementById('radiographCount').value.trim(),
      dictating_doctor_code: document.getElementById('dictatingDoctorCode').value.trim()
    };

    try {
      const result = await createRadiologyRequest(payload);
      if (!result.success) {
        setAlert(result.error || 'No se pudo guardar la solicitud radiológica.');
        return;
      }

      setAlert('Solicitud radiológica guardada correctamente.', 'success');
      form.reset();
      patientIdInput.value = '';
      patientDisplay.value = '';
      patientCedula.value = '';
      patientExpediente.value = '';
      requestDateInput.value = new Date().toISOString().split('T')[0];
    } catch (error) {
      console.error(error);
      setAlert('Error al enviar la solicitud. Intente nuevamente.');
    } finally {
      submitButton.disabled = false;
      submitButton.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i>Enviar solicitud';
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    requestDateInput.value = new Date().toISOString().split('T')[0];
    patientModalInstance = new bootstrap.Modal(patientsModal);

    patientsModal.addEventListener('show.bs.modal', () => {
      loadPatients();
    });
  });
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
