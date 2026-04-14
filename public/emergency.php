<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 data-i18n="emergency_title">Emergency</h2>
      <p class="text-muted mb-0" data-i18n="emergency_description">Emergency request records grouped by patient.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="/solicitud_de_emergency.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="emergency_new_request">New request</span></a>
    </div>
  </div>

  <div id="emergencyListAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="table-responsive">
    <table id="emergencyTable" class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th style="width:5%;" data-i18n="table_index">#</th>
          <th data-i18n="patient">Patient</th>
          <th data-i18n="cedula">Cédula</th>
          <th data-i18n="emergency_table_admission">Admission</th>
          <th data-i18n="emergency_table_service">Service</th>
          <th data-i18n="emergency_table_diagnosis">Admission diagnosis</th>
          <th data-i18n="emergency_table_status">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="7" class="text-center" data-i18n="loading">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
(function() {
  const t = window.i18n_t || (k => k);
  const alertBox = document.getElementById('emergencyListAlert');
  const tableBody = document.querySelector('#emergencyTable tbody');
  const dataTableSelector = '#emergencyTable';

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

  function escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  async function loadEmergencyEntries() {
    setAlert('');
    try {
      const res = await fetch('/api/emergency_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        setAlert(t('emergency_load_failed') || 'Failed to load emergency requests.', 'danger');
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center">' + (t('emergency_no_data') || 'No emergency requests found.') + '</td></tr>';
        return;
      }
      renderRows(json.data);
    } catch (error) {
      console.error(error);
      setAlert(t('emergency_load_error') || 'Error loading emergency records. Please try again.', 'danger');
      tableBody.innerHTML = '<tr><td colspan="7" class="text-center">' + (t('emergency_load_error') || 'Error loading data.') + '</td></tr>';
    }
  }

  function renderRows(entries) {
    if (!Array.isArray(entries) || entries.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="7" class="text-center">' + (t('emergency_no_data') || 'No emergency records found.') + '</td></tr>';
      return;
    }

    entries.sort((a, b) => {
      const nameA = ((a.patient_first_name || '') + ' ' + (a.patient_last_name || '')).toLowerCase();
      const nameB = ((b.patient_first_name || '') + ' ' + (b.patient_last_name || '')).toLowerCase();
      if (nameA !== nameB) return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
      return (b.created_at || '').localeCompare(a.created_at || '');
    });

    let html = '';
    let index = 1;

    entries.forEach(entry => {
      const patientName = `${entry.patient_first_name || ''} ${entry.patient_last_name || ''}`.trim() || (t('patient_unknown') || 'Unknown patient');
      const cedula = entry.cedula || '';
      const formData = entry.form_data || {};
      const admissionDate = formData.admission_date || '';
      const service = formData.admission_service || '';
      const diagnosis = formData.admission_diagnosis || '';
      const status = entry.status || 'activo';

      html += `
        <tr>
          <td>${index++}</td>
          <td>${escapeHtml(patientName)}</td>
          <td>${escapeHtml(cedula)}</td>
          <td>${escapeHtml(admissionDate)}</td>
          <td>${escapeHtml(service)}</td>
          <td>${escapeHtml(diagnosis)}</td>
          <td>${escapeHtml(status)}</td>
        </tr>`;
    });

    tableBody.innerHTML = html;

    if (window.jQuery && window.jQuery.fn.dataTable) {
      if ($.fn.dataTable.isDataTable(dataTableSelector)) {
        $(dataTableSelector).DataTable().destroy();
      }
      $(dataTableSelector).DataTable({
        responsive: true,
        order: [],
        columnDefs: [{ orderable: false, targets: [0] }]
      });
    }
  }

  document.addEventListener('DOMContentLoaded', loadEmergencyEntries);
})();
</script>

<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>
