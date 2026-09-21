<?php $GLOBALS['PAGE_SCRIPTS'][] = 'datatables'; ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0"><i class="fa-solid fa-allergies me-2"></i><span data-i18n="patient_allergies_title">Alergias de pacientes</span></h2>
      <p class="text-muted mb-0" data-i18n="patient_allergies_description">Crear, actualizar y administrar registros de alergias de los pacientes.</p>
    </div>
    <button id="btnAddAllergy" type="button" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="add_allergy">Agregar alergia</span></button>
  </div>

  <div id="allergyAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="allergiesTable" class="table table-striped table-bordered align-middle w-100 mb-0 dt-container">
          <thead>
            <tr>
              <th>ID</th><th>Paciente</th><th>AlÃ©rgeno</th><th>ReacciÃ³n</th><th>Severidad</th><th>Estado</th><th>Fecha</th><th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include APP_ROOT . '/app/Views/Shared/Modals/patient_allergy_modal.php'; ?>

<script>
(function(){
  const alertBox = document.getElementById('allergyAlert');
  const modalEl = document.getElementById('allergyModal');
  let allergyModal = null;
  let allergiesTable = null;
  const preselectedPatientId = new URLSearchParams(window.location.search).get('patient_id');

  function initAllergyModal() {
    if (!allergyModal && modalEl && window.bootstrap) {
      allergyModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
    }
  }

  function showAlert(message) {
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
  }

  function hideAlert() {
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
  }

  function escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  async function loadPatients() {
    const select = document.getElementById('patient_id');
    select.innerHTML = '<option value="">Cargando pacientes...</option>';
    try {
      const response = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await response.json();
      const patients = Array.isArray(json.data) ? json.data : [];
      select.innerHTML = '<option value="">Seleccione un paciente</option>' + patients.map(patient => {
        const name = `${patient.first_name || ''} ${patient.last_name || ''}`.trim();
        return `<option value="${escapeHtml(patient.id)}">${escapeHtml(name)}${patient.cedula ? ' (' + escapeHtml(patient.cedula) + ')' : ''}</option>`;
      }).join('');
      if (preselectedPatientId) select.value = preselectedPatientId;
    } catch (error) {
      select.innerHTML = '<option value="">No se pudieron cargar los pacientes</option>';
    }
  }

  function resetForm() {
    hideAlert();
    document.getElementById('allergyId').value = '';
    document.getElementById('patient_id').value = preselectedPatientId || '';
    document.getElementById('allergen').value = '';
    document.getElementById('reaction').value = '';
    document.getElementById('severity').value = '';
    document.getElementById('status').value = 'active';
    document.getElementById('noted_date').value = '';
    document.getElementById('notes').value = '';
    document.getElementById('allergyModalLabel').textContent = 'Agregar alergia';
  }

  async function initTable() {
    allergiesTable = $('#allergiesTable').DataTable({
            layout: {
        topStart: {
            buttons: ['copy', 'excel', 'pdf', 'colvis']
        }
    },
      ajax: {
        url: '/backend/patient_allergies_fetch.php' + (preselectedPatientId ? '?patient_id=' + encodeURIComponent(preselectedPatientId) : ''),
        dataSrc: 'data'
      },
      columns: [
        { data: 'id' }, { data: 'patient_name', defaultContent: '' }, { data: 'allergen' }, { data: 'reaction', defaultContent: '' },
        { data: 'severity', defaultContent: '' }, { data: 'status' }, { data: 'noted_date', defaultContent: '' },
{ data: null, orderable: false, searchable: false, render: data => `<div class="btn-group" role="group"><button type="button" class="btn btn-sm btn-primary table-action-btn me-1" onclick="editAllergy(${data.id})" title="Editar"><i class="fa-solid fa-pen-to-square"></i><span class="btn-label">Editar</span></button><button type="button" class="btn btn-sm btn-danger table-action-btn" onclick="deleteAllergy(${data.id})" title="Eliminar"><i class="fa-solid fa-trash"></i><span class="btn-label">Eliminar</span></button></div>` }
      ],
      autoWidth: false,
      scrollX: false
    });
  }

  async function saveAllergy(event) {
    event.preventDefault();
    const form = event.currentTarget;
    if (window.ModalSaveState && !window.ModalSaveState.begin(form)) return;
    hideAlert();
    const payload = new URLSearchParams(new FormData(form));
    payload.set('id', document.getElementById('allergyId').value);
    try {
      const response = await fetch('/backend/patient_allergies_save.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload.toString() });
      const json = await response.json();
      if (!response.ok || !json.success) throw new Error(json.error || 'No se pudo guardar la alergia');
      allergyModal.hide();
      allergiesTable.ajax.reload(null, false);
      await window.ModalSaveState?.finish(form, 'Alergia guardada correctamente', 'success');
    } catch (error) {
      showAlert(error.message || 'Error al guardar la alergia');
      await window.ModalSaveState?.finish(form, error.message || 'Error al guardar la alergia', 'error');
    }
  }

  window.editAllergy = async function(id) {
    hideAlert();
    try {
      const response = await fetch(`/backend/patient_allergies_get.php?id=${encodeURIComponent(id)}`, { credentials: 'same-origin' });
      const allergy = await response.json();
      if (!response.ok || !allergy.success) throw new Error(allergy.error || 'Error al cargar');
      document.getElementById('allergyId').value = allergy.id || '';
      document.getElementById('patient_id').value = allergy.patient_id || '';
      document.getElementById('allergen').value = allergy.allergen || '';
      document.getElementById('reaction').value = allergy.reaction || '';
      document.getElementById('severity').value = allergy.severity || '';
      document.getElementById('status').value = allergy.status || 'active';
      document.getElementById('noted_date').value = allergy.noted_date || '';
      document.getElementById('notes').value = allergy.notes || '';
      document.getElementById('allergyModalLabel').textContent = 'Editar alergia';
      allergyModal.show();
    } catch (error) {
      showAlert(error.message || 'Error al cargar la alergia');
    }
  };

  window.deleteAllergy = async function(id) {
    if (!confirm('Â¿Eliminar esta alergia?')) return;
    hideAlert();
    try {
      const response = await fetch('/backend/patient_allergies_delete.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `id=${encodeURIComponent(id)}` });
      const json = await response.json();
      if (!response.ok || !json.success) throw new Error(json.error || 'Error al eliminar');
      allergiesTable.ajax.reload(null, false);
    } catch (error) {
      showAlert(error.message || 'Error al eliminar la alergia');
    }
  };

  document.addEventListener('DOMContentLoaded', async function() {
    await loadPatients();
    resetForm();
    await initTable();
    initAllergyModal();
    document.getElementById('btnAddAllergy').addEventListener('click', () => { resetForm(); allergyModal?.show(); });
    document.getElementById('patientAllergyForm').addEventListener('submit', saveAllergy);
  });
})();
</script>




