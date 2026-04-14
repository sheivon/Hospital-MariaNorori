<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>Radiología</h2>
      <p class="text-muted mb-0">Listado de solicitudes radiológicas por paciente.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="/solicitud_de_radiologia.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i>Nueva solicitud radiológica</a>
    </div>
  </div>

  <div id="radiologyListAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="table-responsive">
    <table id="radiologyTable" class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th style="width:5%;">#</th>
          <th>Paciente</th>
          <th>Cédula</th>
          <th>Fecha solicitud</th>
          <th>Servicio</th>
          <th>Examen solicitado</th>
          <th>Estado</th>
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
  const alertBox = document.getElementById('radiologyListAlert');
  const tableBody = document.querySelector('#radiologyTable tbody');
  const dataTableSelector = '#radiologyTable';

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

  async function loadRadiologyRequests() {
    setAlert('');
    try {
      const res = await fetch('/api/radiologia_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        setAlert('No se pudieron cargar las solicitudes radiológicas.', 'danger');
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No hay solicitudes disponibles.</td></tr>';
        return;
      }
      renderRows(json.data);
    } catch (error) {
      console.error(error);
      setAlert('Error cargando las solicitudes radiológicas. Intente nuevamente.', 'danger');
      tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Error al cargar datos.</td></tr>';
    }
  }

  function renderRows(entries) {
    if (!Array.isArray(entries) || entries.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No hay solicitudes radiológicas registradas.</td></tr>';
      return;
    }

    entries.sort((a, b) => {
      const nameA = (a.patient_name || '').toLowerCase();
      const nameB = (b.patient_name || '').toLowerCase();
      if (nameA !== nameB) return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
      if (a.request_date !== b.request_date) return (b.request_date || '').localeCompare(a.request_date || '');
      return (a.id || 0) - (b.id || 0);
    });

    let html = '';
    let index = 1;

    entries.forEach(entry => {
      const patientName = entry.patient_name || 'Paciente desconocido';
      const cedula = entry.cedula || '';
      const requestDate = entry.request_date || '';
      const service = entry.service || '';
      const examRequested = entry.exam_requested || '';
      const status = entry.status || 'pendiente';

      html += `
        <tr>
          <td>${index++}</td>
          <td>${escapeHtml(patientName)}</td>
          <td>${escapeHtml(cedula)}</td>
          <td>${escapeHtml(requestDate)}</td>
          <td>${escapeHtml(service)}</td>
          <td>${escapeHtml(examRequested)}</td>
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

  document.addEventListener('DOMContentLoaded', () => {
    loadRadiologyRequests();
  });
})();
</script>
<?php include __DIR__ . '/modal/patients_list_modal.php'; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>
