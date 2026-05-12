<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="mx-1 mt-4 px -1">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="fa-solid fa-notes-medical me-2">      
    </i><span data-i18n="encounters">Encounters</span></h2>
      <div class="row g-2 align-items-end">
        <div class="col-auto">
          <label for="encDateFrom" class="form-label mb-1" data-i18n="encounter_filter_date">Encounter date</label>
          <input type="date" id="encDateFrom" class="form-control form-control-sm">
        </div>
        <div class="col-12 col-md-auto">
          <button type="button" id="btnClearEncDate" class="btn btn-outline-secondary btn-sm" data-i18n="clear">Clear</button>
        </div>
      </div>
    <div>
      <button id="btnPrintEncounters" class="btn btn-secondary me-2"><i class="fa-solid fa-print me-1"></i><span data-i18n="print">Print</span></button>
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#encounterModal">
        <i class="fa-solid fa-plus me-1"></i><span data-i18n="add_encounter">Add Encounter</span>
      </button>
    </div>
  </div>
 

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" 
        id="encountersTable" width="100%" >
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="patient">Patient</th>
              <th data-i18n="encounters_date">Date</th>
              <th data-i18n="encounters_type">Type</th>
              <th data-i18n="triage_level">Triage</th>
              <th data-i18n="encounters_status">Status</th>
              <th data-i18n="encounters_doctor">Doctor</th>
              <th data-i18n="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/modal/encounter_modal.php'; ?>
<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const t = window.i18n_t || (k=>k);
  let encounterModalInstance = null;
  let patientPickerModalInstance = null;
  let patientsData = [];
  let doctorsLoaded = false;

  const encounterModal = document.getElementById('encounterModal');
  const encounterForm = document.getElementById('encAddForm');
  const encounterAlert = document.getElementById('encAddAlert');
  const patientsModal = document.getElementById('patientsListModal');
  const patientsModalBody = document.querySelector('#patientsModalSelectionTable tbody');
  const patientsModalMessage = document.getElementById('patientsModalMessage');
  const encDateFromInput = document.getElementById('encDateFrom');
  const btnClearEncDate = document.getElementById('btnClearEncDate');

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function setEncounterAlert(msg){
    if (!encounterAlert) return;
    if (!msg){
      encounterAlert.classList.add('d-none');
      encounterAlert.textContent = '';
      return;
    }
    encounterAlert.classList.remove('d-none');
    encounterAlert.textContent = msg;
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

  function resetEncounterForm(){
    if (!encounterForm) return;
    encounterForm.reset();
    setEncounterAlert('');
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const dateInput = document.getElementById('encAddDate');
    if (dateInput) {
      dateInput.value = now.toISOString().slice(0, 16);
    }
  }

  function formatPatientRow(patient) {
    const name = `${patient.first_name || ''} ${patient.last_name || ''}`.trim();
    return `
      <tr>
        <td>${patient.id}</td>
        <td>${escapeHtml(name)}</td>
        <td>${escapeHtml(patient.cedula || '')}</td>
        <td>${escapeHtml(patient.dob || '')}</td>
        <td>${escapeHtml(patient.email || '')}</td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-primary select-patient-btn table-action-btn" data-patient='${encodeURIComponent(JSON.stringify(patient))}' title="${escapeHtml(t('select') || 'Select')}">
          <i class="fa-solid fa-check"></i><span class="btn-label">${escapeHtml(t('select') || 'Select')}</span>
        </button></td>
      </tr>`;
  }

  function renderPatientsModal() {
    if (!patientsModalBody) return;
    if (!Array.isArray(patientsData) || !patientsData.length) {
      patientsModalBody.innerHTML = `<tr><td colspan="6" class="text-center">${escapeHtml(t('no_patients_available') || 'No patients available')}</td></tr>`;
      return;
    }

    patientsModalBody.innerHTML = patientsData.map(formatPatientRow).join('');
    patientsModalBody.querySelectorAll('.select-patient-btn').forEach(button => {
      button.addEventListener('click', () => {
        const patientJson = decodeURIComponent(button.getAttribute('data-patient') || '');
        try {
          const patient = JSON.parse(patientJson);
          const select = document.getElementById('encAddPatient');
          if (select) {
            select.value = patient.id;
          }
          if (patientPickerModalInstance) {
            patientPickerModalInstance.hide();
          }
        } catch (e) {
          console.error(e);
        }
      });
    });
  }

  async function loadPatients() {
    try {
      const res = await fetch('/api/patients_list.php?encountered=0', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) {
        setModalMessage(t('error_loading_patients') || 'Error loading patients', 'danger');
        return;
      }
      patientsData = Array.isArray(json.data) ? json.data : [];
      const sel = document.getElementById('encAddPatient');
      if (sel) {
        sel.innerHTML = `<option value="">${t('select_patient') || 'Select patient'}</option>` +
          patientsData.map(p => {
            const name = `${p.first_name || ''} ${p.last_name || ''}`.trim();
            return `<option value="${encodeURIComponent(p.id)}">${escapeHtml(name)}</option>`;
          }).join('');
      }
      renderPatientsModal();
      clearModalMessage();
    } catch (e) {
      setModalMessage(t('error_loading_patients') || 'Error loading patients', 'danger');
    }
  }

  async function loadDoctors() {
    if (doctorsLoaded) return;
    try {
      const res = await fetch('/api/users_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success) return;
      const users = Array.isArray(json.data) ? json.data : [];
      const sel = document.getElementById('encAddDoctor');
      if (!sel) return;
      sel.innerHTML = `<option value="">${t('select_doctor') || 'Select doctor'}</option>` +
        users.map(u => `<option value="${encodeURIComponent(u.id)}">${escapeHtml(u.fullname || u.username || String(u.id))}</option>`).join('');
      doctorsLoaded = true;
    } catch (e) {
      console.error(e);
    }
  }

  if ($.fn.dataTable.isDataTable('#encountersTable')) {
    $('#encountersTable').DataTable().destroy();
    $('#encountersTable tbody').off('click');
  }

  const table = $('#encountersTable').DataTable({
    buttons: [
      { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'print', exportOptions: { columns: ':not(:last-child)' } }
    ],
    ajax: {
      url: '/api/encounters_list.php',
      data: function(d){
        if (encDateFromInput && encDateFromInput.value) {
          d.encounter_date = encDateFromInput.value;
        }
      },
      dataSrc: function(json){
        if (!json || !json.success || !Array.isArray(json.data)){
          swal({ title: '', text: t('error') || 'Error loading encounters', icon: 'error' });
          return [];
        }
        return json.data;
      }
    },
    columns: [
      { data: null, render: (data, type, row, meta) => meta.row + 1 },
      { data: null, render: row => escapeHtml(`${row.patient_first_name || ''} ${row.patient_last_name || ''}`.trim()) },
      { data: 'encounter_date', render: d => escapeHtml(d || '') },
      { data: 'encounter_type', render: d => escapeHtml(d || '') },
      { data: 'triage_level', render: d => escapeHtml(d || '') },
      { data: 'status', render: d => escapeHtml(d || '') },
      { data: 'attending_name', render: d => escapeHtml(d || '') },
      { data: 'id', orderable: false, searchable: false, className: 'text-center', render: id => `
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${id}" title="${escapeHtml(t('edit') || 'Edit')}">
              <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${escapeHtml(t('edit') || 'Edit')}</span>
            </button>
            <button class="btn btn-sm btn-danger btn-del table-action-btn" data-id="${id}" title="${escapeHtml(t('delete') || 'Delete')}">
              <i class="fa-solid fa-trash"></i><span class="btn-label">${escapeHtml(t('delete') || 'Delete')}</span>
            </button>
          </div>` }
    ],
    responsive: true,
    lengthMenu: [10, 25, 50, 100]
  });

  $('#encountersTable tbody').on('click', 'button.btn-edit', function(){
    const id = $(this).data('id');
    if (!id) return;
    window.location.href = '/encounter.php?id=' + encodeURIComponent(id);
  });

  $('#encountersTable tbody').on('click', 'button.btn-del', function(){
    const id = $(this).data('id');
    if (!id) return;
    swal({ title: t('delete_confirm') || 'Delete?', icon: 'warning', buttons: [t('cancel')||'Cancel', t('confirm_yes')||'Yes'], dangerMode: true })
      .then(async confirmed => {
        if (!confirmed) return;
        try {
          const res = await fetch('/api/encounters_delete.php', { method: 'POST', credentials: 'same-origin', body: new URLSearchParams({ id }) });
          const json = await res.json();
          if (json.success) {
            table.ajax.reload(null, false);
          } else {
            swal({ title: '', text: json.error || t('error') || 'Error', icon: 'error' });
          }
        } catch (err) {
          swal({ title: '', text: err.message || t('error') || 'Error', icon: 'error' });
        }
      });
  });

  $('#btnPrintEncounters').on('click', function(){
    window.open('/print.php?resource=encounters', '_blank');
  });

  if (encDateFromInput) {
    encDateFromInput.addEventListener('change', function(){
      table.ajax.reload();
    });
  }

  if (btnClearEncDate) {
    btnClearEncDate.addEventListener('click', function(){
      if (!encDateFromInput) return;
      encDateFromInput.value = '';
      table.ajax.reload();
    });
  }

  if (encounterModal) {
    encounterModalInstance = new bootstrap.Modal(encounterModal);
    encounterModal.addEventListener('show.bs.modal', async function(){
      resetEncounterForm();
      await Promise.all([loadPatients(), loadDoctors()]);
    });
  }

  if (patientsModal) {
    patientPickerModalInstance = new bootstrap.Modal(patientsModal);
    patientsModal.addEventListener('show.bs.modal', function(){
      renderPatientsModal();
    });
  }

  if (encounterForm) {
    encounterForm.addEventListener('submit', async function(e){
      e.preventDefault();
      setEncounterAlert('');

      const patientId = document.getElementById('encAddPatient')?.value || '';
      const date = document.getElementById('encAddDate')?.value || '';
      const type = (document.getElementById('encAddType')?.value || '').trim();

      if (!patientId || !date || !type) {
        setEncounterAlert(t('fix_errors') || 'Please complete required fields.');
        return;
      }

      const payload = new URLSearchParams();
      payload.append('patient_id', patientId);
      payload.append('encounter_date', date);
      payload.append('encounter_type', type);
      payload.append('status', document.getElementById('encAddStatus')?.value || 'open');

      const triageValue = (document.getElementById('encAddTriage')?.value || '').trim();
      if (triageValue) payload.append('triage_level', triageValue);

      const doctorValue = document.getElementById('encAddDoctor')?.value || '';
      if (doctorValue) payload.append('attending_user_id', doctorValue);

      const reasonValue = (document.getElementById('encAddReason')?.value || '').trim();
      if (reasonValue) payload.append('reason_for_visit', reasonValue);

      const notesValue = (document.getElementById('encAddNotes')?.value || '').trim();
      if (notesValue) payload.append('notes', notesValue);

      try {
        const res = await fetch('/api/encounters_create.php', {
          method: 'POST',
          credentials: 'same-origin',
          body: payload
        });
        const json = await res.json();
        if (!json.success) {
          setEncounterAlert(json.error || t('error') || 'Error');
          return;
        }

        if (encounterModalInstance) {
          encounterModalInstance.hide();
        }
        table.ajax.reload(null, false);
        swal({ title: '', text: t('encounter_saved') || 'Encounter created', icon: 'success' });
      } catch (err) {
        setEncounterAlert(err.message || t('error') || 'Error');
      }
    });
  }
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
