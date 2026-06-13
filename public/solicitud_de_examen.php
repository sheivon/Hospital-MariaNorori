<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Solicitud de Examen</h2>
    <a href="/" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Volver</a>
  </div>

  <div id="examAlert" class="alert alert-danger d-none" role="alert"></div>

  <form id="examRequestForm" class="row g-3">
    <input type="hidden" id="patientId" name="patient_id">

    <div class="col-md-4">
      <label for="patientDisplay" class="form-label">Paciente</label>
      <div class="input-group">
        <input type="text" id="patientDisplay" class="form-control" readonly placeholder="Seleccione un paciente">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#patientsListModal">Buscar paciente</button>
      </div>
    </div>

    <div class="col-md-2">
      <label for="patientCedula" class="form-label">Cédula</label>
      <input type="text" id="patientCedula" class="form-control" readonly>
    </div>

    <div class="col-md-2">
      <label for="patientExpediente" class="form-label">Expediente</label>
      <input type="text" id="patientExpediente" class="form-control" readonly>
    </div>

    <div class="col-md-2">
      <label for="requestDate" class="form-label">Fecha de solicitud</label>
      <input type="date" id="requestDate" name="request_date" class="form-control" required>
    </div>

    <div class="col-12">
      <label class="form-label">Exámenes solicitados</label>
      <div class="card card-body bg-light">
        <div class="row gy-3">
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Química sanguínea</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Glucosa" id="examGlucosa"><label class="form-check-label" for="examGlucosa">Glucosa</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Urea" id="examUrea"><label class="form-check-label" for="examUrea">Urea</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Creatinina" id="examCreatinina"><label class="form-check-label" for="examCreatinina">Creatinina</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Nitrógeno de Urea" id="examNitrogeno"><label class="form-check-label" for="examNitrogeno">Nitrógeno de Urea</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Ácido Úrico" id="examAcidoUrico"><label class="form-check-label" for="examAcidoUrico">Ácido Úrico</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Hemoglobina Glicosilada" id="examHemoglobina"><label class="form-check-label" for="examHemoglobina">Hemoglobina Glicosilada</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Glucosa Post-Prandial" id="examPostPrandial"><label class="form-check-label" for="examPostPrandial">Glucosa Post-Prandial</label></div>
          </div>
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Tolerancia a la glucosa</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Ayunas" id="examAyunas"><label class="form-check-label" for="examAyunas">Ayunas</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="30 minutos" id="exam30Min"><label class="form-check-label" for="exam30Min">30 minutos</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="60 minutos" id="exam60Min"><label class="form-check-label" for="exam60Min">60 minutos</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="120 minutos" id="exam120Min"><label class="form-check-label" for="exam120Min">120 minutos</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="180 minutos" id="exam180Min"><label class="form-check-label" for="exam180Min">180 minutos</label></div>
          </div>
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Lípidos y electrolitos</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Colesterol" id="examColesterol"><label class="form-check-label" for="examColesterol">Colesterol</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Triglicéridos" id="examTrigliceridos"><label class="form-check-label" for="examTrigliceridos">Triglicéridos</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="HDL-Colesterol" id="examHDL"><label class="form-check-label" for="examHDL">HDL-Colesterol</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="LDL-Colesterol" id="examLDL"><label class="form-check-label" for="examLDL">LDL-Colesterol</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="VLDL-Colesterol" id="examVLDL"><label class="form-check-label" for="examVLDL">VLDL-Colesterol</label></div>
          </div>
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Proteínas y bilirrubinas</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Proteínas Totales" id="examProteinas"><label class="form-check-label" for="examProteinas">Proteínas Totales</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Albúminas" id="examAlbuminas"><label class="form-check-label" for="examAlbuminas">Albúminas</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Globulinas" id="examGlobulinas"><label class="form-check-label" for="examGlobulinas">Globulinas</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Relación A/G" id="examAG"><label class="form-check-label" for="examAG">Relación A/G</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Fosfatasa Alcalina" id="examFosfatasa"><label class="form-check-label" for="examFosfatasa">Fosfatasa Alcalina</label></div>
          </div>
        </div>

        <div class="row gy-3 mt-3"> 
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Enzimas</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="T.G. Pirúvica" id="examTGPiruvica"><label class="form-check-label" for="examTGPiruvica">T.G. Pirúvica</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="T.G. Oxalacética" id="examTGOxalacetica"><label class="form-check-label" for="examTGOxalacetica">T.G. Oxalacética</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Lacto Deshidrogenasa" id="examLDH"><label class="form-check-label" for="examLDH">Lacto Deshidrogenasa</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="CPK - Total" id="examCPKTotal"><label class="form-check-label" for="examCPKTotal">CPK - Total</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="CPK - MB" id="examCPKMB"><label class="form-check-label" for="examCPKMB">CPK - MB</label></div>
          </div>
          <div class="col-12 col-md-3">
            <h6 class="mb-2">Otros</h6>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Amilasa Total" id="examAmilasaTotal"><label class="form-check-label" for="examAmilasaTotal">Amilasa Total</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Amilasa Pancreática" id="examAmilasaPancreatica"><label class="form-check-label" for="examAmilasaPancreatica">Amilasa Pancreática</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Lipasa" id="examLipasa"><label class="form-check-label" for="examLipasa">Lipasa</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Sodio" id="examSodio"><label class="form-check-label" for="examSodio">Sodio</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Potasio" id="examPotasio"><label class="form-check-label" for="examPotasio">Potasio</label></div>
          </div> 
          <div class="col-12 col-md-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Cloro" id="examCloro"><label class="form-check-label" for="examCloro">Cloro</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Fósforo" id="examFosforo"><label class="form-check-label" for="examFosforo">Fósforo</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Magnesio" id="examMagnesio"><label class="form-check-label" for="examMagnesio">Magnesio</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Hierro sérico" id="examHierro"><label class="form-check-label" for="examHierro">Hierro sérico</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Fijación de Hierro" id="examFijacion"><label class="form-check-label" for="examFijacion">Fijación de Hierro</label></div>
          </div>
          <div class="col-12 col-md-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Calcio" id="examCalcio"><label class="form-check-label" for="examCalcio">Calcio</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Troponina I Cualitativa" id="examTroponina"><label class="form-check-label" for="examTroponina">Troponina I Cualitativa</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Dímero D Cualitativa" id="examDimero"><label class="form-check-label" for="examDimero">Dímero D Cualitativa</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" name="exam_types[]" value="Proteína C Reactiva Cuantitativa" id="examPCR"><label class="form-check-label" for="examPCR">Proteína C Reactiva Cuantitativa</label></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <label for="notes" class="form-label">Notas</label>
      <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Información adicional..."></textarea>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/" class="btn btn-secondary">Cancelar</a>
      <button id="submitExamRequest" class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane me-1"></i>Enviar solicitud</button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>

<script>
(function() {
  const alertBox = document.getElementById('examAlert');
  const form = document.getElementById('examRequestForm');
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
    alertBox.classList.add(`alert-${type}`);
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

  function formatPatientRow(patient) {
    const name = [patient.first_name, patient.last_name].filter(Boolean).join(' ');
    const cedula = patient.cedula || '';
    const expediente = patient.expediente_no || '';
    return `
      <tr>
        <td>${patient.id}</td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(cedula)}</td>
        <td>${escapeHtml(patient.dob || '')}</td>
        <td>${escapeHtml(patient.email || '')}</td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-primary select-patient-btn table-action-btn" data-patient='${encodeURIComponent(JSON.stringify(patient))}' title="Seleccionar">
            <i class="fa-solid fa-check"></i><span class="btn-label">Seleccionar</span>
          </button>
        </td>
      </tr>
    `;
  }

  function escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function selectPatient(patient) {
    patientIdInput.value = patient.id;
    patientDisplay.value = [patient.first_name, patient.last_name].filter(Boolean).join(' ');
    patientCedula.value = patient.cedula || '';
    patientExpediente.value = patient.expediente_no || '';
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

      const rows = json.data.map(p => formatPatientRow(p)).join('');
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

  async function createExamRequests(patientId, examTypes, date, notes) {
    const requestPromises = examTypes.map(type => {
      return fetch('/api/exam_requests_create.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          patient_id: Number(patientId),
          exam_type: type,
          request_date: date,
          notes: notes
        })
      }).then(res => res.json());
    });

    return Promise.all(requestPromises);
  }

  form.addEventListener('submit', async event => {
    event.preventDefault();
    setAlert('');

    const patientId = patientIdInput.value.trim();
    const examTypes = Array.from(document.querySelectorAll('input[name="exam_types[]"]:checked')).map(el => el.value);
    const requestDate = requestDateInput.value;
    const notes = document.getElementById('notes').value.trim();

    if (!patientId) {
      setAlert('Seleccione un paciente antes de enviar la solicitud.');
      return;
    }
    if (!requestDate) {
      setAlert('Ingrese la fecha de solicitud.');
      return;
    }
    if (examTypes.length === 0) {
      setAlert('Seleccione al menos un examen.');
      return;
    }

    const submitButton = document.getElementById('submitExamRequest');
    submitButton.disabled = true;
    submitButton.textContent = 'Guardando...';

    try {
      const results = await createExamRequests(patientId, examTypes, requestDate, notes);
      const failed = results.filter(r => !r.success);
      if (failed.length > 0) {
        setAlert('Algunos exámenes no se guardaron correctamente.');
        console.error('Exam request errors', failed);
      } else {
        if (window.swal) {
          window.swal('Solicitud enviada', 'La solicitud de examen se ha guardado correctamente.', 'success');
        } else {
          alert('La solicitud de examen se ha guardado correctamente.');
        }
        form.reset();
        patientIdInput.value = '';
        patientDisplay.value = '';
        patientCedula.value = '';
        patientExpediente.value = '';
        requestDateInput.value = new Date().toISOString().split('T')[0];
      }
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
