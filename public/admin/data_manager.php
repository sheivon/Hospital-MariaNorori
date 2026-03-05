<?php
require_once __DIR__ . '/../../src/auth.php';
require_role('admin');
include __DIR__ . '/../../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="data_manager_title">Data Manager</h3>
    <div class="d-flex gap-2">
      <button id="btnCreateRow" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="data_manager_new">New</span></button>
      <button id="btnRefreshRows" class="btn btn-secondary"><i class="fa-solid fa-rotate me-1"></i><span data-i18n="data_manager_refresh">Refresh</span></button>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-body d-flex align-items-center gap-3">
      <label for="tableSelector" class="mb-0" data-i18n="data_manager_table">Table</label>
      <select id="tableSelector" class="form-select" style="max-width: 360px"></select>
      <small class="text-muted" data-i18n="data_manager_softdelete_hint">Deletes are soft delete (sets deleted_at).</small>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-2">
      <div id="dmError" class="alert alert-danger d-none mb-2"></div>
      <div class="table-responsive">
        <table class="table table-striped table-sm" id="dmTable">
          <thead></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="dmModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="dmForm">
        <div class="modal-header">
          <h5 class="modal-title" id="dmModalTitle" data-i18n="data_manager_edit_row">Edit Row</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="dmFormError" class="alert alert-danger d-none"></div>
          <input type="hidden" id="dmRowId">
          <div id="dmFormFields" class="row g-3"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><span data-i18n="cancel">Cancel</span></button>
          <button class="btn btn-primary"><span data-i18n="save">Save</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
<script>
(function(){
  const t = window.i18n_t || ((k)=>k);
  const tr = (key, fallback) => {
    const value = t(key);
    return value === key ? fallback : value;
  };

  const tableSelector = document.getElementById('tableSelector');
  const dmError = document.getElementById('dmError');
  const dmTableHead = document.querySelector('#dmTable thead');
  const dmTableBody = document.querySelector('#dmTable tbody');
  const btnCreateRow = document.getElementById('btnCreateRow');
  const btnRefreshRows = document.getElementById('btnRefreshRows');
  const modal = new bootstrap.Modal(document.getElementById('dmModal'));
  const dmForm = document.getElementById('dmForm');
  const dmFormFields = document.getElementById('dmFormFields');
  const dmFormError = document.getElementById('dmFormError');
  const dmRowId = document.getElementById('dmRowId');
  const dmModalTitle = document.getElementById('dmModalTitle');

  let currentColumns = [];
  let currentRows = [];
  let currentTable = '';
  let currentPk = 'id';

  function showError(message){
    if (!message){ dmError.classList.add('d-none'); dmError.textContent = ''; return; }
    dmError.textContent = message;
    dmError.classList.remove('d-none');
  }

  function showFormError(message){
    if (!message){ dmFormError.classList.add('d-none'); dmFormError.textContent = ''; return; }
    dmFormError.textContent = message;
    dmFormError.classList.remove('d-none');
  }

  function labelFor(name){
    return name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  }

  function editableColumns(){
    return currentColumns
      .map(c => c.Field)
      .filter(name => !['created_at','updated_at','deleted_at'].includes(name));
  }

  function renderTable(){
    const columns = currentColumns.map(c => c.Field).filter(f => f !== 'deleted_at');
    dmTableHead.innerHTML = '<tr>' + columns.map(c => `<th>${c}</th>`).join('') + `<th>${tr('actions','Actions')}</th></tr>`;

    if (!Array.isArray(currentRows) || currentRows.length === 0){
      dmTableBody.innerHTML = `<tr><td colspan="${columns.length + 1}" class="text-center text-muted">${tr('no_data','No data')}</td></tr>`;
      return;
    }

    dmTableBody.innerHTML = '';
    currentRows.forEach(row => {
      const tr = document.createElement('tr');
      tr.innerHTML = columns.map(c => `<td>${escapeHtml(row[c])}</td>`).join('') +
        `<td>
          <button class="btn btn-sm btn-primary me-1 btn-edit" data-id="${row[currentPk]}"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-sm btn-danger btn-del" data-id="${row[currentPk]}"><i class="fa-solid fa-trash"></i></button>
        </td>`;
      dmTableBody.appendChild(tr);
    });
  }

  function escapeHtml(value){
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  async function loadTables(){
    showError('');
    const res = await fetch('/api/admin/tables_meta.php');
    const j = await res.json();
    if (!j.success){ throw new Error(j.error || tr('data_manager_failed_tables', 'Failed loading tables')); }

    const entries = Object.entries(j.tables || {});
    tableSelector.innerHTML = entries.map(([name, meta]) => `<option value="${name}">${meta.label || name}</option>`).join('');
    if (entries.length > 0){
      currentTable = entries[0][0];
      tableSelector.value = currentTable;
      currentPk = entries[0][1].pk || 'id';
    }
  }

  async function loadRows(){
    if (!currentTable){ return; }
    showError('');
    const res = await fetch('/api/admin/table_rows.php?table=' + encodeURIComponent(currentTable));
    const j = await res.json();
    if (!j.success){ throw new Error(j.error || tr('data_manager_failed_rows', 'Failed loading rows')); }
    currentColumns = j.columns || [];
    currentRows = j.rows || [];
    renderTable();
  }

  function openCreate(){
    showFormError('');
    dmRowId.value = '';
    dmModalTitle.textContent = tr('data_manager_create_row', 'Create Row');
    buildFormFields({});
    modal.show();
  }

  function openEdit(id){
    const row = currentRows.find(r => String(r[currentPk]) === String(id));
    if (!row){ return; }
    showFormError('');
    dmRowId.value = String(id);
    dmModalTitle.textContent = tr('data_manager_update_row', 'Update Row');
    buildFormFields(row);
    modal.show();
  }

  function buildFormFields(row){
    const fields = editableColumns().filter(name => name !== currentPk);
    dmFormFields.innerHTML = '';

    fields.forEach(name => {
      const col = document.createElement('div');
      col.className = 'col-md-6';

      const value = row[name] ?? '';
      const isLong = name.includes('notes') || name.includes('description') || name.includes('message') || String(value).length > 120;
      const input = isLong
        ? `<textarea class="form-control" id="f_${name}" rows="3">${escapeHtml(value)}</textarea>`
        : `<input class="form-control" id="f_${name}" value="${escapeHtml(value)}">`;

      col.innerHTML = `<label class="form-label" for="f_${name}">${labelFor(name)}</label>${input}`;
      dmFormFields.appendChild(col);
    });
  }

  function gatherPayload(){
    const data = {};
    const fields = editableColumns().filter(name => name !== currentPk);
    fields.forEach(name => {
      const el = document.getElementById('f_' + name);
      if (!el) return;
      data[name] = el.value;
    });
    return data;
  }

  async function createRow(data){
    const res = await fetch('/api/admin/table_create.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ table: currentTable, data })
    });
    return res.json();
  }

  async function updateRow(id, data){
    const res = await fetch('/api/admin/table_update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ table: currentTable, id, data })
    });
    return res.json();
  }

  async function deleteRow(id){
    const res = await fetch('/api/admin/table_delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ table: currentTable, id })
    });
    return res.json();
  }

  tableSelector.addEventListener('change', async () => {
    currentTable = tableSelector.value;
    try {
      const metaRes = await fetch('/api/admin/tables_meta.php');
      const metaJson = await metaRes.json();
      currentPk = (metaJson.tables?.[currentTable]?.pk) || 'id';
      await loadRows();
    } catch (e) {
      showError(e.message || 'Error');
    }
  });

  btnCreateRow.addEventListener('click', openCreate);
  btnRefreshRows.addEventListener('click', () => loadRows().catch(e => showError(e.message || 'Error')));

  dmTableBody.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('.btn-edit');
    const delBtn = e.target.closest('.btn-del');

    if (editBtn){
      openEdit(editBtn.dataset.id);
      return;
    }

    if (delBtn){
      const id = delBtn.dataset.id;
      const ok = confirm(tr('data_manager_soft_delete_confirm', 'Soft delete this row?'));
      if (!ok) return;
      const j = await deleteRow(id);
      if (!j.success){ showError(j.error || tr('data_manager_delete_failed', 'Delete failed')); return; }
      await loadRows();
    }
  });

  dmForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    showFormError('');
    const id = dmRowId.value;
    const payload = gatherPayload();

    const j = id ? await updateRow(id, payload) : await createRow(payload);
    if (!j.success){
      showFormError(j.error || tr('data_manager_save_failed', 'Save failed'));
      return;
    }

    modal.hide();
    await loadRows();
  });

  (async function init(){
    try {
      await loadTables();
      await loadRows();
    } catch (e) {
      showError(e.message || tr('data_manager_init_failed', 'Initialization error'));
    }
  })();
})();
</script>
