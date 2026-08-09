<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4" id="medicationsPage">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">
      <i class="fa-solid fa-pills me-2"></i>
      <span data-i18n="medications_catalog">Medications Catalog</span>
    </h2>
    <div class="d-flex align-items-center gap-2">
      <input type="search" id="medSearch" class="form-control form-control-sm" placeholder="Search…" style="min-width:200px">
      <button id="btnMedAdd" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#medicationModal">
        <i class="fa-solid fa-plus me-1"></i><span data-i18n="medications_catalog_add">Add Medication</span>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" id="medicationsTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="medications_catalog_name">Medication name</th>
              <th data-i18n="medications_catalog_generic">Generic name</th>
              <th data-i18n="medications_catalog_form">Form</th>
              <th data-i18n="medications_catalog_strength">Strength</th>
              <th data-i18n="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Medication Modal -->
<div class="modal fade" id="medicationModal" tabindex="-1" aria-labelledby="medicationModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="medicationModalTitle" data-i18n="medications_catalog_add">Add Medication</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="medAlert" class="alert alert-danger d-none" role="alert"></div>
        <form id="medForm" class="row g-3">
          <input type="hidden" id="medId" name="id">

          <div class="col-12">
            <label for="medName" class="form-label" data-i18n="medications_catalog_name">Medication name</label>
            <input type="text" class="form-control" id="medName" name="medication_name" required maxlength="200">
            <div class="invalid-feedback" id="medNameError"></div>
          </div>

          <div class="col-md-6">
            <label for="medGeneric" class="form-label" data-i18n="medications_catalog_generic">Generic name</label>
            <input type="text" class="form-control" id="medGeneric" name="generic_name" maxlength="200">
          </div>

          <div class="col-md-3">
            <label for="medForm" class="form-label" data-i18n="medications_catalog_form">Form</label>
            <input type="text" class="form-control" id="medForm" name="form" maxlength="100" placeholder="tablet, syrup…">
          </div>

          <div class="col-md-3">
            <label for="medStrength" class="form-label" data-i18n="medications_catalog_strength">Strength</label>
            <input type="text" class="form-control" id="medStrength" name="strength" maxlength="100" placeholder="500 mg">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
        <button id="btnSaveMed" class="btn btn-primary" type="button">
          <i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const tblBody = document.querySelector('#medicationsTable tbody');
  const form = document.getElementById('medForm');
  const alertBox = document.getElementById('medAlert');
  const modalEl = document.getElementById('medicationModal');
  const searchInput = document.getElementById('medSearch');
  let dataTable = null;
  let allRows = [];

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  function setAlert(msg){
    if (!alertBox) return;
    if (!msg){ alertBox.classList.add('d-none'); alertBox.textContent = ''; return; }
    alertBox.classList.remove('d-none');
    alertBox.textContent = msg;
  }

  function setFieldError(id, msg){
    const input = document.getElementById(id);
    const error = document.getElementById(id + 'Error');
    if (!input || !error) return;
    if (!msg){ input.classList.remove('is-invalid'); error.textContent = ''; return; }
    input.classList.add('is-invalid');
    error.textContent = msg;
  }

  function resetForm(){
    if (!form) return;
    form.reset();
    document.getElementById('medId').value = '';
    setFieldError('medName', '');
    setAlert('');
    const title = document.getElementById('medicationModalTitle');
    if (title) {
      title.setAttribute('data-i18n', 'medications_catalog_add');
      title.textContent = t('medications_catalog_add') || 'Add Medication';
    }
  }

  function renderRows(rows){
    if (!tblBody) return;
    if (!rows.length){
      tblBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${escapeHtml(t('medications_catalog_empty') || 'No medications in the catalog yet')}</td></tr>`;
      return;
    }
    tblBody.innerHTML = rows.map((r, i) => `
      <tr>
        <td>${i + 1}</td>
        <td>${escapeHtml(r.medication_name || '')}</td>
        <td>${escapeHtml(r.generic_name || '')}</td>
        <td>${escapeHtml(r.form || '')}</td>
        <td>${escapeHtml(r.strength || '')}</td>
        <td class="text-center">
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${r.id}" title="${escapeHtml(t('edit') || 'Edit')}">
              <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${escapeHtml(t('edit') || 'Edit')}</span>
            </button>
            <button class="btn btn-sm btn-danger btn-del table-action-btn" data-id="${r.id}" title="${escapeHtml(t('delete') || 'Delete')}">
              <i class="fa-solid fa-trash"></i><span class="btn-label">${escapeHtml(t('delete') || 'Delete')}</span>
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  function initDataTable(){
    if (typeof $ === 'undefined' || !$.fn.dataTable) {
      window.addEventListener('load', initDataTable, { once: true });
      return;
    }
    if ($.fn.dataTable.isDataTable('#medicationsTable')) {
      $('#medicationsTable').DataTable().destroy();
    }
    dataTable = $('#medicationsTable').DataTable({
      responsive: true,
      pageLength: 25,
      lengthMenu: [10, 25, 50, 100],
      order: [[1, 'asc']],
      columnDefs: [
        { orderable: false, searchable: false, targets: [0, 5] }
      ]
    });
  }

  async function loadMedications(){
    if (!tblBody) return;
    tblBody.innerHTML = `<tr><td colspan="6" class="text-center">${escapeHtml(t('loading') || 'Loading...')}</td></tr>`;
    try {
      const res = await fetch('/api/medications_catalog_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){
        tblBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${escapeHtml(json.error || t('error') || 'Error')}</td></tr>`;
        return;
      }
      allRows = Array.isArray(json.rows) ? json.rows : [];
      renderRows(allRows);
      initDataTable();
    } catch (e) {
      tblBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${escapeHtml(t('error') || 'Error')}</td></tr>`;
    }
  }

  function findRowById(id){
    return allRows.find(r => String(r.id) === String(id)) || null;
  }

  function openEditModal(id){
    const row = findRowById(id);
    if (!row) return;
    resetForm();
    document.getElementById('medId').value = row.id || '';
    document.getElementById('medName').value = row.medication_name || '';
    document.getElementById('medGeneric').value = row.generic_name || '';
    document.getElementById('medForm').value = row.form || '';
    document.getElementById('medStrength').value = row.strength || '';
    const title = document.getElementById('medicationModalTitle');
    if (title) {
      title.setAttribute('data-i18n', 'medications_catalog_edit');
      title.textContent = t('medications_catalog_edit') || 'Edit Medication';
    }
    if (modalEl && window.bootstrap) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
  }

  async function submitForm(){
    setFieldError('medName', '');
    setAlert('');

    const id = document.getElementById('medId').value;
    const name = document.getElementById('medName').value.trim();
    if (!name) {
      setFieldError('medName', t('medications_catalog_required') || 'Medication name is required');
      setAlert(t('medications_catalog_required') || 'Medication name is required');
      return;
    }

    const payload = {
      medication_name: name,
      generic_name: document.getElementById('medGeneric').value.trim(),
      form: document.getElementById('medForm').value.trim(),
      strength: document.getElementById('medStrength').value.trim(),
    };
    if (id) payload.id = Number(id);

    const url = id ? '/api/medications_catalog_update.php' : '/api/medications_catalog_create.php';

    try {
      const res = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!json.success) {
        setAlert(json.error || t('error') || 'Error');
        return;
      }
      if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
      }
      resetForm();
      swal({ title: '', text: t('medications_catalog_saved') || 'Medication saved', icon: 'success' });
      loadMedications();
    } catch (e) {
      setAlert(t('error') || 'Error');
    }
  }

  async function deleteMedication(id){
    try {
      const ok = await swal({
        title: t('delete_confirm') || 'Delete?',
        icon: 'warning',
        buttons: [t('cancel') || 'Cancel', t('confirm_yes') || 'Yes'],
        dangerMode: true
      });
      if (!ok) return;
      const res = await fetch('/api/medications_catalog_delete.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(id) })
      });
      const json = await res.json();
      if (!json.success) {
        swal({ title: '', text: json.error || t('error') || 'Error', icon: 'error' });
        return;
      }
      swal({ title: '', text: t('medications_catalog_deleted') || 'Medication deleted', icon: 'success' });
      loadMedications();
    } catch (e) {
      swal({ title: '', text: e.message || t('error') || 'Error', icon: 'error' });
    }
  }

  // Wire up events
  if (tblBody) {
    tblBody.addEventListener('click', (e) => {
      const editBtn = e.target.closest('button.btn-edit');
      if (editBtn) {
        const id = editBtn.dataset.id;
        if (id) openEditModal(id);
        return;
      }
      const delBtn = e.target.closest('button.btn-del');
      if (delBtn) {
        const id = delBtn.dataset.id;
        if (id) deleteMedication(id);
      }
    });
  }

  const btnAdd = document.getElementById('btnMedAdd');
  if (btnAdd) {
    btnAdd.addEventListener('click', resetForm);
  }

  const btnSave = document.getElementById('btnSaveMed');
  if (btnSave) {
    btnSave.addEventListener('click', submitForm);
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      if (dataTable) dataTable.search(searchInput.value).draw();
    });
  }

  // Bootstrap modal backdrop cleanup (matches Appointments.js pattern)
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', () => {
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      if (document.body.classList.contains('modal-open')) {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
      }
    });
  }

  // Initial load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadMedications);
  } else {
    loadMedications();
  }
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
