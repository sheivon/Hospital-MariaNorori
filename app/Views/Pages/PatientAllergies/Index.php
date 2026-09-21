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
<table id="allergiesTable" class="table table-sm table-striped dt-container" width="100%">
          <thead>
            <tr>
              <th>#</th><th data-i18n="patient">Paciente</th><th data-i18n="allergen">Alérgeno</th><th data-i18n="reaction">Reacción</th><th data-i18n="severity">Severidad</th><th data-i18n="table_status">Estado</th><th data-i18n="noted_date">Fecha</th><th data-i18n="actions">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
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
    if ($.fn.dataTable.isDataTable('#allergiesTable')) {
      $('#allergiesTable').DataTable().destroy();
    }
    allergiesTable = $('#allergiesTable').DataTable({
      ajax: {
        url: '/backend/patient_allergies_fetch.php' + (preselectedPatientId ? '?patient_id=' + encodeURIComponent(preselectedPatientId) : ''),
        dataSrc: 'data'
      },
      layout: {
        topStart: {
          buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
        }
      },
      buttons: [
        { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
        {
          extend: 'print',
          exportOptions: { columns: ':not(:last-child)' },
          action: function () {
            if (typeof window.triggerCustomPrint === 'function') {
              window.triggerCustomPrint('allergies', preselectedPatientId ? { patient_id: preselectedPatientId } : {});
              return;
            }
            const url = '/print.php?resource=allergies' + (preselectedPatientId ? '&patient_id=' + encodeURIComponent(preselectedPatientId) : '');
            window.open(url, '_blank');
          }
        },
        { extend: 'colvis' }
      ],
      responsive: true,
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [[6, 'desc']],
      stateSave: false,
      columns: [
        { data: null, orderable: false, searchable: false, render: (data, type, row, meta) => meta.row + 1 },
        { data: 'patient_name', defaultContent: '', render: d => escapeHtml(d || '') },
        { data: 'allergen', render: d => escapeHtml(d || '') },
        { data: 'reaction', defaultContent: '', render: d => escapeHtml(d || '') },
        { data: 'severity', defaultContent: '', render: d => escapeHtml(d || '') },
        { data: 'status', render: d => escapeHtml(d || '') },
        { data: 'noted_date', defaultContent: '', render: d => escapeHtml(d || '') },
        { data: 'id', orderable: false, searchable: false, className: 'text-center', render: id => `<div class="btn-group" role="group"><button type="button" class="btn btn-sm btn-primary table-action-btn me-1" onclick="editAllergy(${id})" title="Editar"><i class="fa-solid fa-pen-to-square"></i><span class="btn-label">Editar</span></button><button type="button" class="btn btn-sm btn-danger table-action-btn" onclick="deleteAllergy(${id})" title="Eliminar"><i class="fa-solid fa-trash"></i><span class="btn-label">Eliminar</span></button></div>` }
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
      const d = allergy.data || allergy;
      document.getElementById('allergyId').value = d.id || '';
      document.getElementById('patient_id').value = d.patient_id || '';
      document.getElementById('allergen').value = d.allergen || '';
      document.getElementById('reaction').value = d.reaction || '';
      document.getElementById('severity').value = d.severity || '';
      document.getElementById('status').value = d.status || 'active';
      document.getElementById('noted_date').value = d.noted_date || '';
      document.getElementById('notes').value = d.notes || '';
      document.getElementById('allergyModalLabel').textContent = 'Editar alergia';
      allergyModal.show();
    } catch (error) {
      showAlert(error.message || 'Error al cargar la alergia');
    }
  };

  window.deleteAllergy = async function(id) {
    if (!confirm('¿Eliminar esta alergia?')) return;
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




