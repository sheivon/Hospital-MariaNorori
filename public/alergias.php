<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include(__DIR__ . '/../templates/header.php');
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0"><i class="fa-solid fa-allergies me-2"></i>Alergias de pacientes</h2>
      <p class="text-muted mb-0">Crear, actualizar y administrar registros de alergias de los pacientes.</p>
    </div>
    <button id="btnAddAllergy" type="button" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i>Agregar alergia</button>
  </div>

  <div id="allergyAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="border rounded border-2 border-success"> 
    <div class="p-3">
      <div class="table-responsive">
        <table id="allergiesTable" class="table table-striped table-bordered mb-0" style="width:100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Paciente</th>
              <th>Alérgeno</th>
              <th>Reacción</th>
              <th>Severidad</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/modal/patient_allergy_modal.php'; ?>

<script>
(function(){
  const alertBox = document.getElementById('allergyAlert');
  const modalEl = document.getElementById('allergyModal');
  let allergyModal = null;
  let allergiesTable = null;

  function initAllergyModal() {
    if (!allergyModal && modalEl && window.bootstrap) {
      allergyModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
    }
  }

  function showAlert(message) {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
  }

  function hideAlert() {
    if (!alertBox) return;
    alertBox.textContent = '';
    alertBox.classList.add('d-none');
  }

  function escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  const urlParams = new URLSearchParams(window.location.search);
  const preselectedPatientId = urlParams.get('patient_id');

  async function loadPatients() {
    const select = document.getElementById('patient_id');
    if (!select) return;
    select.innerHTML = '<option value="">Cargando pacientes...</option>';
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      const patients = Array.isArray(json.data) ? json.data : [];
      select.innerHTML = '<option value="">Seleccione un paciente</option>' + patients.map(p => {
        const name = `${p.first_name || ''} ${p.last_name || ''}`.trim();
        return `<option value="${escapeHtml(p.id)}">${escapeHtml(name)}${p.cedula ? ' (' + escapeHtml(p.cedula) + ')' : ''}</option>`;
      }).join('');
      if (preselectedPatientId) {
        select.value = preselectedPatientId;
      }
    } catch (err) {
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
    if (allergiesTable) {
      allergiesTable.ajax.reload();
      return;
    }

    allergiesTable = $('#allergiesTable').DataTable({
      ajax: {
        url: '/backend/patient_allergies_fetch.php' + (preselectedPatientId ? '?patient_id=' + encodeURIComponent(preselectedPatientId) : ''),
        dataSrc: 'data'
      },
      columns: [
        { data: 'id' },
        { data: 'patient_name', defaultContent: '' },
        { data: 'allergen' },
        { data: 'reaction', defaultContent: '' },
        { data: 'severity', defaultContent: '' },
        { data: 'status' },
        { data: 'noted_date', defaultContent: '' },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function(data) {
            return `
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-sm btn-primary table-action-btn me-1" onclick="editAllergy(${data.id})" title="Editar">
                <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">Editar</span>
              </button>
              <button type="button" class="btn btn-sm btn-danger table-action-btn" onclick="deleteAllergy(${data.id})" title="Eliminar">
                <i class="fa-solid fa-trash"></i><span class="btn-label">Eliminar</span>
              </button>
            </div>
            `;
          }
        }
      ]
    });
  }

  async function saveAllergy(event) {
    event.preventDefault();
    hideAlert();

    const payload = new URLSearchParams();
    payload.set('id', document.getElementById('allergyId').value);
    payload.set('patient_id', document.getElementById('patient_id').value);
    payload.set('allergen', document.getElementById('allergen').value);
    payload.set('reaction', document.getElementById('reaction').value);
    payload.set('severity', document.getElementById('severity').value);
    payload.set('status', document.getElementById('status').value);
    payload.set('noted_date', document.getElementById('noted_date').value);
    payload.set('notes', document.getElementById('notes').value);

    try {
      const res = await fetch('/backend/patient_allergies_save.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        throw new Error(json.error || 'No se pudo guardar la alergia');
      }
      allergyModal.hide();
      allergiesTable.ajax.reload(null, false);
    } catch (err) {
      showAlert(err.message || 'Error al guardar la alergia');
    }
  }

  window.editAllergy = async function(id) {
    hideAlert();
    try {
      const res = await fetch(`/backend/patient_allergies_get.php?id=${encodeURIComponent(id)}`, { credentials: 'same-origin' });
      const json = await res.json();
      if (!res.ok) throw new Error(json.error || 'Error al cargar');
      document.getElementById('allergyId').value = json.id || '';
      document.getElementById('patient_id').value = json.patient_id || '';
      document.getElementById('allergen').value = json.allergen || '';
      document.getElementById('reaction').value = json.reaction || '';
      document.getElementById('severity').value = json.severity || '';
      document.getElementById('status').value = json.status || 'active';
      document.getElementById('noted_date').value = json.noted_date || '';
      document.getElementById('notes').value = json.notes || '';
      document.getElementById('allergyModalLabel').textContent = 'Editar alergia';
      allergyModal.show();
    } catch (err) {
      showAlert(err.message || 'Error al cargar la alergia');
    }
  };

  window.deleteAllergy = async function(id) {
    if (!confirm('¿Eliminar esta alergia?')) return;
    hideAlert();
    try {
      const res = await fetch('/backend/patient_allergies_delete.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}`
      });
      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.error || 'Error al eliminar');
      allergiesTable.ajax.reload(null, false);
    } catch (err) {
      showAlert(err.message || 'Error al eliminar la alergia');
    }
  };

  document.addEventListener('DOMContentLoaded', async function() {
    await loadPatients();
    resetForm();
    await initTable();
    initAllergyModal();

    document.getElementById('btnAddAllergy').addEventListener('click', function() {
      resetForm();
      if (allergyModal) {
        allergyModal.show();
      }
    });

    const allergyForm = document.getElementById('patientAllergyForm');
    if (allergyForm) {
      allergyForm.addEventListener('submit', saveAllergy);
    }
  });
})();
</script>

<?php include('../templates/footer.php'); ?>