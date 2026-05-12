class UsersApi {
  static async request(url, options = {}) {
    const response = await fetch(url, { credentials: 'same-origin', ...options });
    if (!response.ok) {
      throw new Error(`Network error: ${response.status}`);
    }

    const json = await response.json();
    if (!json.success) {
      const error = new Error(json.error || 'API error');
      error.api = json;
      throw error;
    }

    return json;
  }

  static list() {
    return this.request('/api/admin/users_list.php');
  }

  static roles() {
    return this.request('/api/admin/roles_list.php');
  }

  static save(userId, payload) {
    const url = userId ? '/api/admin/user_update.php' : '/api/admin/user_create.php';
    return this.request(url, { method: 'POST', body: payload });
  }

  static delete(id) {
    return this.request('/api/admin/user_delete.php', {
      method: 'POST',
      body: new URLSearchParams({ id }),
    });
  }
}

class UserModal {
  constructor() {
    this.modalElement = document.getElementById('userModal');
    this.form = document.getElementById('userForm');
    this.errorBox = document.getElementById('userFormError');
    this.title = document.getElementById('userModalTitle');
    this.modal = new bootstrap.Modal(this.modalElement);
  }

  setTitle(text) {
    this.title.textContent = text;
  }

  reset() {
    this.form.reset();
    this.hideError();
    document.getElementById('username').required = true;
    document.getElementById('password').required = true;
  }

  fill(user) {
    document.getElementById('userId').value = user.id || '';
    document.getElementById('username').value = user.username || '';
    document.getElementById('username').required = false;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('fullname').value = user.fullname || '';
    document.getElementById('cedula').value = user.cedula || '';
    document.getElementById('specialty').value = user.specialty || '';
    document.getElementById('department').value = user.department || '';
  }

  async loadRoles(selected = 'user') {
    const select = document.getElementById('role');
    if (!select) return;

    try {
      const { data: roles } = await UsersApi.roles();
      select.innerHTML = roles.map(role => `
        <option value="${UserView.escapeHtml(role.role)}">${UserView.escapeHtml(role.role)}</option>
      `).join('');
      select.value = selected;
      if (select.value !== selected) {
        select.selectedIndex = 0;
      }
    } catch (error) {
      select.innerHTML = '<option value="user">user</option>';
      select.value = 'user';
    }
  }

  open(user = null) {
    if (!user) {
      this.reset();
      this.setTitle('Create user');
      this.loadRoles('user');
    } else {
      this.reset();
      this.fill(user);
      this.setTitle('Update user');
      this.loadRoles(user.role || 'user');
    }

    this.modal.show();
  }

  close() {
    this.modal.hide();
  }

  serialize() {
    return new FormData(this.form);
  }

  showError(message) {
    if (!this.errorBox) return;
    this.errorBox.classList.remove('d-none');
    this.errorBox.textContent = message;
  }

  hideError() {
    if (!this.errorBox) return;
    this.errorBox.classList.add('d-none');
    this.errorBox.textContent = '';
  }
}

class UserView {
  static table = null;
  static modal = null;

  static escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  static async init() {
    if (!document.querySelector('#usersTable')) return;
    this.modal = new UserModal();
    this.initTable();
    this.bindActions();
  }

  static initTable() {
    if (window.jQuery && $.fn.dataTable.isDataTable('#usersTable')) {
      $('#usersTable').DataTable().destroy();
      $('#usersTable').find('tbody').off('click');
      this.table = null;
    }

    const t = window.i18n_t || (key => key);
    this.table = $('#usersTable').DataTable({
      dom: 'Bfrtip',
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
              window.triggerCustomPrint('users');
              return;
            }
            window.open('/print.php?resource=users', '_blank');
          }
        }
      ],
      ajax: {
        url: '/api/admin/users_list.php',
        dataSrc(json) {
          if (!json || !json.success || !Array.isArray(json.data)) {
            swal({ title: '', text: t('error') || 'Error loading users', icon: 'error' });
            return [];
          }
          return json.data;
        }
      },
      columns: [
        { data: 'id' },
        { data: 'username', render: d => UserView.escapeHtml(d) },
        { data: 'fullname', render: d => UserView.escapeHtml(d || '') },
        { data: 'cedula', render: d => UserView.escapeHtml(d || '') },
        { data: 'role', render: d => UserView.escapeHtml(d || '') },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center d-flex',
          render(row) {
            return `
              <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${row.id}" title="${t('edit') || 'Edit'}">
                <i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${t('edit') || 'Edit'}</span>
              </button>
              <button class="btn btn-sm btn-danger btn-del table-action-btn" data-id="${row.id}" title="${t('delete') || 'Delete'}">
                <i class="fa-solid fa-trash"></i><span class="btn-label">${t('delete') || 'Delete'}</span>
              </button>
            `;
          }
        }
      ],
      responsive: true
    });
  }

  static bindActions() {
    const createButton = document.getElementById('btnNewUser');
    const printButton = document.getElementById('btnPrintUsers');

    createButton?.addEventListener('click', () => this.modal.open());

    $('#usersTable tbody').off('click').on('click', 'button', async function() {
      const button = $(this);
      const id = button.data('id');
      if (!id) return;

      if (button.hasClass('btn-edit')) {
        const row = UserView.table.row(button.closest('tr')).data();
        if (!row) return;
        UserView.modal.open(row);
      }

      if (button.hasClass('btn-del')) {
        const t = window.i18n_t || (key => key);
        swal({
          title: t('delete_user_confirm'),
          text: '',
          icon: 'warning',
          buttons: [t('cancel'), t('confirm_yes')],
          dangerMode: true
        }).then(async confirmed => {
          if (!confirmed) return;
          try {
            await UsersApi.delete(id);
            UserView.reloadTable();
          } catch (error) {
            swal({ title: '', text: error.message || t('error'), icon: 'error' });
          }
        });
      }
    });

    this.modal.form.addEventListener('submit', async event => {
      event.preventDefault();
      this.modal.hideError();

      const formData = this.modal.serialize();
      const userId = formData.get('id');

      try {
        await UsersApi.save(userId, formData);
        this.modal.close();
        this.reloadTable();
      } catch (error) {
        this.modal.showError(error.message || 'Unable to save user');
      }
    });

    printButton?.addEventListener('click', () => {
      const table = document.getElementById('usersTable');
      if (!table) return swal({ title: '', text: (window.i18n_t?.('no_table_to_print') || 'No table to print'), icon: 'info' });
      if (typeof window.triggerCustomPrint === 'function') {
        window.triggerCustomPrint('users');
        return;
      }
      window.open('/print.php?resource=users', '_blank');
    });
  }

  static reloadTable() {
    if (this.table) {
      this.table.ajax.reload(null, false);
    }
  }
}

window.addEventListener('DOMContentLoaded', () => UserView.init());
