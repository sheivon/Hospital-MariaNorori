<?php
require_once __DIR__ . '/../../src/auth.php';
require_role('admin');
include __DIR__ . '/../../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="admin_users">Admin</h3>
    <div>
      <button id="btnPrintUsers" class="btn btn-secondary me-2"><i class="fa-solid fa-print me-1"></i><span data-i18n="print">Print</span></button>
      <button id="btnNewUser" class="btn btn-success"><i class="fa-solid fa-user-plus me-1"></i><span data-i18n="create_user">Create user</span></button>
    </div>
  </div>
  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-striped table-sm" id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th data-i18n="label_username">Username</th>
              <th data-i18n="label_fullname">Full name</th>
              <th data-i18n="label_cedula">Cédula</th>
              <th data-i18n="role">Role</th>
              <th data-i18n="actions" class="align-center content-center" width="6%">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle" data-i18n="create_user">Create user</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="userFormError" class="alert alert-danger d-none"></div>
          <input type="hidden" name="id" id="userId">
          <div class="mb-3">
            <label class="form-label" data-i18n="label_username">Username</label>
            <input class="form-control" name="username" id="username">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_password">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="" data-i18n="password_optional_reset">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_fullname">Full name</label>
            <input class="form-control" name="fullname" id="fullname">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_cedula">Cédula</label>
            <input class="form-control" name="cedula" id="cedula">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="role">Role</label>
            <select class="form-select" name="role" id="role"></select>
          </div>
          <small class="text-muted" data-i18n="password_optional_reset">Leave password empty to keep current (when editing)</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="Close">Close</button>
          <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i><span data-i18n="save">Save</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Wait for jQuery and DataTables to be available before initializing
function waitForLibraries(callback, timeoutMs = 5000) {
  const start = Date.now();
  const iv = setInterval(() => {
    if (window.jQuery && $.fn && $.fn.dataTable) {
      clearInterval(iv);
      callback();
      return;
    }
    if (Date.now() - start > timeoutMs) {
      clearInterval(iv);
      console.error('Timed out waiting for jQuery/DataTables to load');
    }
  }, 50);
}

// Define the main initialization function
function runUsersInit(){
  const t = window.i18n_t || (k=>k);
  const tableBody = document.querySelector('#usersTable tbody');
  const modal = new bootstrap.Modal(document.getElementById('userModal'));
  const form = document.getElementById('userForm');
  const errBox = document.getElementById('userFormError');
  const title = document.getElementById('userModalTitle');
  const roleSelect = document.getElementById('role');

  async function loadRoles(selectedValue){
    if (!roleSelect) return;
    try {
      const res = await fetch('/api/admin/roles_list.php');
      const j = await res.json();
      const roles = (j && j.success && Array.isArray(j.data)) ? j.data : [];
      if (!roles.length) {
        roleSelect.innerHTML = '<option value="user">user</option>';
        roleSelect.value = 'user';
        return;
      }

      roleSelect.innerHTML = roles.map(r => `<option value="${escapeHtml(r.role)}">${escapeHtml(r.role)}</option>`).join('');
      const preferred = selectedValue || roleSelect.value || 'user';
      roleSelect.value = preferred;
      if (roleSelect.value !== preferred) {
        roleSelect.selectedIndex = 0;
      }
    } catch (e) {
      roleSelect.innerHTML = '<option value="user">user</option>';
      roleSelect.value = 'user';
    }
  }

  // Initialize DataTable to load users via AJAX
  let usersTable = null;
  function initUsersTable(){
    if (!window.jQuery || !$.fn.dataTable) { console.error('DataTables not available'); return; }
    usersTable = $('#usersTable').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'print', exportOptions: { columns: ':not(:last-child)' } }
      ],
      ajax: {
        url: '/api/admin/users_list.php',
        dataSrc: function(json){
          if (!json || !json.success || !Array.isArray(json.data)){
            console.error('Invalid users list response', json);
            swal({ title:'', text: t('error')||'Error loading users', icon:'error'});
            return [];
          }
          return json.data;
        }
      },
      columns: [
        { data: 'id' },
        { data: 'username', render: function(d){ return escapeHtml(d); } },
        { data: 'fullname', render: function(d){ return escapeHtml(d||''); } },
        { data: 'cedula', render: function(d){ return escapeHtml(d||''); } },
        { data: 'role', render: function(d){ return escapeHtml(d||''); } },
        { data: null, orderable: false, searchable: false, className: 'text-center', render: function(row){
            return `<button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}"><i class="fa-solid fa-pen-to-square"></i></button> <button class="btn btn-sm btn-danger btn-del" data-id="${row.id}"><i class="fa-solid fa-trash"></i></button>`;
        }}
      ],
      responsive: true
    });

    // delegated handlers for edit/delete using DataTable rows
    $('#usersTable tbody').off('click').on('click', 'button', async function(e){
      const $btn = $(this);
      const id = $btn.data('id');
      if ($btn.hasClass('btn-edit')){
        // fetch specific user via the table data (if present) or API fallback
        const rowData = usersTable.row($btn.closest('tr')).data();
        let u = rowData;
        if (!u){
          const res = await fetch('/api/admin/users_list.php');
          const j = await res.json();
          u = j.data.find(x=>String(x.id)===String(id));
        }
        if (!u) return;
        errBox.classList.add('d-none'); errBox.textContent='';
        document.getElementById('userId').value = u.id;
        document.getElementById('username').value = u.username;
        document.getElementById('username').required = false;
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
        document.getElementById('fullname').value = u.fullname||'';
        document.getElementById('cedula').value = u.cedula||'';
        await loadRoles(u.role || 'user');
        title.textContent = t('update_user');
        modal.show();
      } else if ($btn.hasClass('btn-del')){
        swal({
          title: t('delete_user_confirm'), text: '', type:'warning', showCancelButton:true,
          confirmButtonColor:'#d33', confirmButtonText: t('confirm_yes'), cancelButtonText: t('cancel'), closeOnConfirm:true
        }, async (isConfirm)=>{
          if (!isConfirm) return;
          const r = await fetch('/api/admin/user_delete.php', { method:'POST', body: new URLSearchParams({id}) });
          const j = await r.json();
          if (j.success) usersTable.ajax.reload(); else swal({title:'', text: j.error||t('error'), type:'error'});
        });
      }
    });
  }
  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  document.getElementById('btnNewUser').addEventListener('click', ()=>{
    form.reset(); errBox.classList.add('d-none'); errBox.textContent='';
    document.getElementById('userId').value='';
    document.getElementById('username').required = true;
    document.getElementById('password').required = true;
    loadRoles('user');
    title.textContent = t('create_user');
    modal.show();
  });

  // initialize DataTable and handlers
  loadRoles('user');
  initUsersTable();

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    errBox.classList.add('d-none'); errBox.textContent='';
    const data = new FormData(form);
    const id = data.get('id');
    const url = id ? '/api/admin/user_update.php' : '/api/admin/user_create.php';
    try{
      const res = await fetch(url, { method:'POST', body: data });
      const j = await res.json();
      if (j.success){ modal.hide(); usersTable.ajax.reload(); }
      else { errBox.classList.remove('d-none'); errBox.textContent = j.error||t('error'); }
    }catch(err){ errBox.classList.remove('d-none'); errBox.textContent = t('error'); }
  });
  

  // Print users table
  document.getElementById('btnPrintUsers')?.addEventListener('click', ()=>{
    try{
      const tnow = new Date().toLocaleString();
      const table = document.getElementById('usersTable');
      if (!table) return swal({ title:'', text: t('no_table_to_print')||'No table to print', type:'info' });

      // clone so we don't alter page table
      const cloned = table.cloneNode(true);
      // find actions column index
      const headers = Array.from(cloned.querySelectorAll('thead th'));
      let actionIndex = headers.findIndex(th => th.getAttribute('data-i18n') === 'actions' || th.textContent.trim().toLowerCase() === 'actions');
      if (actionIndex === -1) {
        // fallback: look for buttons column (contains 'Action' or empty)
        actionIndex = headers.findIndex(th => /action|acciones|acciones/i.test(th.textContent));
      }
      if (actionIndex !== -1) {
        cloned.querySelectorAll('tr').forEach(row => {
          const cells = row.querySelectorAll('th, td');
          if (cells[actionIndex]) cells[actionIndex].remove();
        });
      }

      cloned.className = 'table table-sm table-striped table-bordered print-table';
      const html = `<!doctype html><html><head><meta charset="utf-8"><title>${t('admin_users')||'Users'}</title><link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"><link href="/assets/css/styles.css" rel="stylesheet"><style>@media print{ .no-print{display:none!important} }</style></head><body><div class="container mt-4"><div class="d-flex align-items-center mb-3"><h2 class="mb-0">${t('admin_users')||'Users'}</h2><small class="text-muted ms-3">${tnow}</small></div><div class="table-responsive">${cloned.outerHTML}</div></div><script>try{window.print();}catch(e){};setTimeout(()=>window.close(),800);</script></body></html>`;

      const w = window.open('','_blank');
      w.document.write(html);
      w.document.close();
      w.focus();
    }catch(e){ console.error('print users', e); swal({ title:'', text: t('error')||'Error', type:'error' }); }
  });
}

// wait for DataTables/jQuery then run init (works whether DOMContentLoaded already fired or not)
waitForLibraries(() => {
  if (document.readyState !== 'loading') runUsersInit(); else document.addEventListener('DOMContentLoaded', runUsersInit);
});
// removed duplicate module-based DataTable init; users table is initialized above with Buttons support
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
