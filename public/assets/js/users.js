class UsersDataLayer {
  static async request(url, options = {}) {
    const resp = await fetch(url, { credentials: 'same-origin', ...options });
    if (!resp.ok) throw new Error('Network error');
    const json = await resp.json();
    if (!json.success) {
      const err = new Error(json.error || 'API error');
      err.api = json;
      throw err;
    }
    return json;
  }

  static async list() {
    return UsersDataLayer.request('/api/admin/users_list.php');
  }

  static async roles() {
    return UsersDataLayer.request('/api/admin/roles_list.php');
  }

  static async create(payload) {
    return UsersDataLayer.request('/api/admin/user_create.php', { method:'POST', body: payload });
  }

  static async update(payload) {
    return UsersDataLayer.request('/api/admin/user_update.php', { method:'POST', body: payload });
  }

  static async delete(id) {
    return UsersDataLayer.request('/api/admin/user_delete.php', { method:'POST', body: new URLSearchParams({ id }) });
  }
}

class UsersView {
  static escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  static async renderRoleOptions(selected){
    const select = document.getElementById('role');
    if (!select) return;
    try {
      const result = await UsersDataLayer.roles();
      const roles = Array.isArray(result.data) ? result.data : [];
      select.innerHTML = roles.map(r=>`<option value="${this.escapeHtml(r.role)}">${this.escapeHtml(r.role)}</option>`).join('');
      select.value = selected || 'user';
      if (select.value !== (selected || 'user')) select.selectedIndex = 0;
    } catch (e) {
      select.innerHTML = '<option value="user">user</option>';
      select.value = 'user';
    }
  }

  static init(){
    if (!document.querySelector('#usersTable')) return;

    const t = window.i18n_t || (k=>k);
    const table = $('#usersTable').DataTable({
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
            swal({ title:'', text: t('error')||'Error loading users', icon: 'error'});
            return [];
          }
          return json.data;
        }
      },
      columns: [
        { data: 'id' },
        { data: 'username', render: d => UsersView.escapeHtml(d) },
        { data: 'fullname', render: d => UsersView.escapeHtml(d||'') },
        { data: 'cedula', render: d => UsersView.escapeHtml(d||'') },
        { data: 'role', render: d => UsersView.escapeHtml(d||'') },
        { data: null, orderable: false, searchable: false, className: 'text-center d-flex ', render: row => `
            <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="btn btn-sm btn-danger btn-del" data-id="${row.id}"><i class="fa-solid fa-trash"></i></button>
          ` }
      ],
      responsive: true
    });

    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const form = document.getElementById('userForm');
    const errorBox = document.getElementById('userFormError');
    const title = document.getElementById('userModalTitle');

    async function clearAndOpenEdit(user){
      if (!form) return;
      form.reset();
      errorBox.classList.add('d-none');
      errorBox.textContent = '';

      document.getElementById('userId').value = user.id || '';
      document.getElementById('username').value = user.username || '';
      document.getElementById('username').required = false;
      document.getElementById('password').value = '';
      document.getElementById('password').required = false;
      document.getElementById('fullname').value = user.fullname || '';
      document.getElementById('cedula').value = user.cedula || '';
      document.getElementById('specialty').value = user.specialty || '';
      document.getElementById('department').value = user.department || '';
      await UsersView.renderRoleOptions(user.role || 'user');
      title.textContent = t('update_user');
      modal.show();
    }

    document.getElementById('btnNewUser')?.addEventListener('click', async () => {
      form.reset();
      errorBox.classList.add('d-none');
      errorBox.textContent = '';
      document.getElementById('userId').value = '';
      document.getElementById('username').required = true;
      document.getElementById('password').required = true;
      await UsersView.renderRoleOptions('user');
      title.textContent = t('create_user');
      modal.show();
    });

    $('#usersTable tbody').off('click').on('click', 'button', async function(){
      const btn = $(this);
      const id = btn.data('id');
      if (!id) return;

      if (btn.hasClass('btn-edit')){
        const row = table.row(btn.closest('tr')).data();
        if (!row) return;
        await clearAndOpenEdit(row);
      }

      if (btn.hasClass('btn-del')){
        swal({
          title: t('delete_user_confirm'), text:'', icon:'warning', buttons:[t('cancel'),t('confirm_yes')], dangerMode:true
        }).then(async confirmed => {
          if (!confirmed) return;
          try {
            await UsersDataLayer.delete(id);
            table.ajax.reload();
          } catch (err){
            swal({ title:'', text: err.message||t('error'), icon:'error' });
          }
        });
      }
    });

    form.addEventListener('submit', async (e)=>{
      e.preventDefault();
      errorBox.classList.add('d-none');
      errorBox.textContent = '';

      const data = new FormData(form);
      const id = data.get('id');
      const url = id ? '/api/admin/user_update.php' : '/api/admin/user_create.php';

      try {
        const res = await UsersDataLayer.request(url, { method:'POST', body: data });
        if (res.success){
          modal.hide();
          table.ajax.reload();
        } else {
          throw new Error(res.error || t('error'));
        }
      } catch (err){
        errorBox.classList.remove('d-none');
        errorBox.textContent = err.message || t('error');
      }
    });

    document.getElementById('btnPrintUsers')?.addEventListener('click', () => {
      const current = document.getElementById('usersTable');
      if (!current) return swal({ title:'', text: t('no_table_to_print')||'No table to print', icon:'info' });
      window.open('/print.php?resource=users', '_blank');
    });
  }
}

window.addEventListener('DOMContentLoaded', () => UsersView.init());
