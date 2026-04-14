<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 data-i18n="exams_title">Exámenes</h2>
      <p class="text-muted mb-0" data-i18n="exams_description">Listado de solicitudes de examen agrupadas por paciente y fecha.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="/solicitud_de_examen.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="exam_request_button">Solicitud de examen</span></a>
    </div>
  </div>

  <div id="examListAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="table-responsive">
    <table id="examsTable" class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th style="width:10%;" data-i18n="table_index">#</th>
          <th data-i18n="exam_table_patient">Paciente</th>
          <th data-i18n="exam_table_cedula">Cédula</th>
          <th data-i18n="exam_table_date">Fecha</th>
          <th data-i18n="exam_table_type">Examen</th>
          <th data-i18n="exam_table_notes">Notas</th>
          <th data-i18n="exam_table_status">Estado</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="7" class="text-center">Cargando...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
(function() {
  const t = window.i18n_t || (k => k);
  const alertBox = document.getElementById('examListAlert');
  const tableBody = document.querySelector('#examsTable tbody');
  const dataTableSelector = '#examsTable';

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

  async function loadExamRequests() {
    setAlert('');
    try {
      const res = await fetch('/api/exam_requests_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        setAlert(t('exam_load_failed') || 'No se pudieron cargar los exámenes.', 'danger');
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center">${t('exam_no_data') || 'No hay exámenes disponibles.'}</td></tr>`;
        return;
      }

      renderExamRows(json.data);
    } catch (err) {
      console.error(err);
      setAlert(t('exam_load_failed') || 'Error cargando los exámenes. Intente nuevamente.', 'danger');
      tableBody.innerHTML = `<tr><td colspan="7" class="text-center">${t('exam_load_error') || 'Error al cargar datos.'}</td></tr>`;
    }
  }

  function renderExamRows(exams) {
    if (!Array.isArray(exams) || exams.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="7" class="text-center">${t('exam_no_data') || 'No hay exámenes solicitados.'}</td></tr>`;
      return;
    }

    exams.sort((a, b) => {
      const patientA = (a.patient_name || '').toLowerCase();
      const patientB = (b.patient_name || '').toLowerCase();
      if (patientA !== patientB) return patientA.localeCompare(patientB, 'es', { sensitivity: 'base' });
      if (a.request_date !== b.request_date) return a.request_date.localeCompare(b.request_date);
      return (a.exam_type || '').localeCompare(b.exam_type || '', 'es', { sensitivity: 'base' });
    });

    let rowsHtml = '';
    let index = 1;

    exams.forEach(exam => {
      const patientName = exam.patient_name || t('patient_unknown') || 'Paciente desconocido';
      const date = exam.request_date || t('no_date') || 'Sin fecha';

      rowsHtml += `
        <tr>
          <td>${index++}</td>
          <td>${escapeHtml(patientName)}</td>
          <td>${escapeHtml(exam.cedula || '')}</td>
          <td>${escapeHtml(date)}</td>
          <td>${escapeHtml(exam.exam_type || '')}</td>
          <td>${escapeHtml(exam.notes || '')}</td>
          <td>${escapeHtml(exam.status || '')}</td>
        </tr>`;
    });

    tableBody.innerHTML = rowsHtml;

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

  document.addEventListener('DOMContentLoaded', () => {
    loadExamRequests();
  });
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>