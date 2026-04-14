class AdolescentHistoryDataLayer {
  static async request(url, options = {}) {
    const response = await fetch(url, { credentials: 'same-origin', ...options });
    if (!response.ok) {
      throw new Error('Network error');
    }
    const json = await response.json();
    if (!json.success) {
      throw new Error(json.error || 'API error');
    }
    return json;
  }

  static async list() {
    return this.request('/api/adolescent_histories_list.php');
  }

  static async create(payload) {
    return this.request('/api/adolescent_histories_create.php', { method: 'POST', body: payload });
  }
}

class AdolescentHistoryView {
  static dataTable = null;

  static escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  static async init() {
    if (!document.querySelector('#adolescentHistoryPage')) return;
    this.form = document.getElementById('adolescentHistoryForm');
    this.errorBox = document.getElementById('adolescentHistoryError');

    await this.loadPatients();
    await this.loadList();

    if (this.form) {
      this.form.addEventListener('submit', (event) => this.submitForm(event));
    }
  }

  static async loadPatients() {
    const select = document.getElementById('patient_id');
    if (!select) return;
    select.innerHTML = '<option value="">Loading...</option>';

    try {
      const json = await fetch('/api/patients_list.php', { credentials: 'same-origin' }).then((r) => r.json());
      const patients = Array.isArray(json.data) ? json.data : [];
      select.innerHTML = '<option value="">Select patient</option>' + patients.map((p) => {
        const name = `${p.first_name || ''} ${p.last_name || ''}`.trim();
        return `<option value="${this.escapeHtml(p.id)}">${this.escapeHtml(name)}${p.cedula ? ' (' + this.escapeHtml(p.cedula) + ')' : ''}</option>`;
      }).join('');
    } catch (error) {
      select.innerHTML = '<option value="">Unable to load patients</option>';
    }
  }

  static async loadList() {
    const tableBody = document.querySelector('#adolescentHistoryTable tbody');
    if (!tableBody) return;

    try {
      const result = await AdolescentHistoryDataLayer.list();
      const rows = Array.isArray(result.data) ? result.data : [];
      tableBody.innerHTML = rows.length ? rows.map((row) => `
        <tr>
          <td>${this.escapeHtml(row.id)}</td>
          <td>${this.escapeHtml((row.patient_first_name || '') + ' ' + (row.patient_last_name || ''))}</td>
          <td>${this.escapeHtml(row.cedula || '')}</td>
          <td>${this.escapeHtml(row.visit_date || '')}</td>
          <td>${this.escapeHtml(row.reason_for_consultation || '').slice(0, 120)}</td>
          <td>${this.escapeHtml(row.created_at || '')}</td>
        </tr>
      `).join('') : '<tr><td colspan="6" class="text-center">No records yet</td></tr>';
      this.initDataTable();
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${this.escapeHtml(error.message)}</td></tr>`;
    }
  }

  static initDataTable() {
    if (!window.jQuery || !window.jQuery.fn.DataTable) return;
    if ($.fn.dataTable.isDataTable('#adolescentHistoryTable')) {
      $('#adolescentHistoryTable').DataTable().destroy();
      $('#adolescentHistoryTable').find('tbody').off('click');
    }

    this.dataTable = $('#adolescentHistoryTable').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50],
      order: [[0, 'desc']],
      columnDefs: [{ orderable: false, targets: [4] }]
    });
  }

  static async submitForm(event) {
    event.preventDefault();
    if (!this.form) return;

    this.setError('');
    const formData = new FormData(this.form);
    if (!formData.get('patient_id')) {
      this.setError(window.i18n_t ? window.i18n_t('patient_required') : 'Please select a patient');
      return;
    }

    try {
      await AdolescentHistoryDataLayer.create(formData);
      this.form.reset();
      await this.loadList();
      this.setError(window.i18n_t ? window.i18n_t('history_saved') : 'Record saved successfully', false);
    } catch (error) {
      this.setError(error.message || 'Unable to save record');
    }
  }

  static setError(message, isError = true) {
    if (!this.errorBox) return;
    this.errorBox.textContent = message;
    if (!message) {
      this.errorBox.classList.add('d-none');
      return;
    }
    this.errorBox.classList.remove('d-none');
    this.errorBox.classList.toggle('text-danger', isError);
    this.errorBox.classList.toggle('text-success', !isError);
  }
}

window.addEventListener('DOMContentLoaded', () => {
  AdolescentHistoryView.init();
});
