class TestsDataLayer {
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

  static async list(query = '') {
    const url = '/api/tests_list.php' + (query ? `?${query}` : '');
    return this.request(url);
  }

  static async create(payload) {
    return this.request('/api/tests_create.php', { method: 'POST', body: payload });
  }

  static async patients() {
    return this.request('/api/patients_list.php');
  }
}

class TestsView {
  static dataTable = null;

  static escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  static async init() {
    if (!document.querySelector('#testsPage')) return;
    this.initModal();
    await this.loadTests();
  }

  static testModal = null;

  static initModal() {
    const modalEl = document.getElementById('testModal');
    const form = document.getElementById('testForm');
    if (!modalEl || !form || !window.bootstrap) return;

    this.testModal = new bootstrap.Modal(modalEl);

    modalEl.addEventListener('show.bs.modal', async () => {
      this.resetTestForm();
      await this.loadPatientsForForm();
    });

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      await this.saveTest();
    });
  }

  static setFormError(message) {
    const errorBox = document.getElementById('testFormError');
    if (!errorBox) return;
    if (!message) {
      errorBox.classList.add('d-none');
      errorBox.textContent = '';
      return;
    }
    errorBox.classList.remove('d-none');
    errorBox.textContent = message;
  }

  static resetTestForm() {
    const form = document.getElementById('testForm');
    if (!form) return;
    form.reset();
    this.setFormError('');
    const dt = document.getElementById('test_date');
    if (dt) {
      const now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      dt.value = now.toISOString().slice(0, 16);
    }
  }

  static async loadPatientsForForm() {
    const select = document.getElementById('test_patient_id');
    if (!select) return;
    select.innerHTML = `<option value="">${window.i18n_t ? window.i18n_t('loading') : 'Loading...'}</option>`;
    try {
      const result = await TestsDataLayer.patients();
      const rows = Array.isArray(result.data) ? result.data : [];
      select.innerHTML = `<option value="">${window.i18n_t ? window.i18n_t('select_patient') : 'Select patient'}</option>` + rows.map((p) => {
        const fullName = `${p.first_name || ''} ${p.last_name || ''}`.trim();
        const ced = p.cedula ? ` (${this.escapeHtml(p.cedula)})` : '';
        return `<option value="${encodeURIComponent(p.id)}">${this.escapeHtml(fullName)}${ced}</option>`;
      }).join('');
    } catch (error) {
      select.innerHTML = `<option value="">${window.i18n_t ? window.i18n_t('error') : 'Error'}</option>`;
      this.setFormError(error.message || 'Error loading patients');
    }
  }

  static async saveTest() {
    this.setFormError('');
    const form = document.getElementById('testForm');
    const submitBtn = document.getElementById('btnSaveTest');
    if (!form) return;

    const payload = new URLSearchParams();
    payload.set('patient_id', document.getElementById('test_patient_id')?.value || '');
    payload.set('test_type', (document.getElementById('test_type')?.value || '').trim());
    payload.set('result', (document.getElementById('result')?.value || '').trim());
    payload.set('test_date', document.getElementById('test_date')?.value || '');
    payload.set('notes', (document.getElementById('notes')?.value || '').trim());

    if (!payload.get('patient_id') || !payload.get('test_type')) {
      this.setFormError(window.i18n_t ? window.i18n_t('fix_errors') : 'Please complete required fields.');
      return;
    }

    if (submitBtn) submitBtn.disabled = true;
    try {
      await TestsDataLayer.create(payload);
      if (this.testModal) this.testModal.hide();
      await this.loadTests();
      if (window.swal) {
        swal({ title: '', text: window.i18n_t ? (window.i18n_t('saved') || 'Saved') : 'Saved', icon: 'success' });
      }
    } catch (error) {
      this.setFormError(error.message || 'Save failed');
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  }

  static async loadTests() {
    const tableBody = document.querySelector('#testsTable tbody');
    if (!tableBody) return;

    try {
      const urlParams = new URLSearchParams(window.location.search);
      const query = urlParams.toString();
      const result = await TestsDataLayer.list(query);
      const rows = Array.isArray(result.data) ? result.data : [];
      tableBody.innerHTML = rows.length ? rows.map((row) => {
        const patientLink = row.patient_id ? `<a href="/paciente.php?id=${encodeURIComponent(row.patient_id)}">${this.escapeHtml((row.patient_first_name || '') + ' ' + (row.patient_last_name || ''))}</a>` : this.escapeHtml(row.patient_first_name || '');
        return `
          <tr>
            <td>${this.escapeHtml(row.id)}</td>
            <td>${this.escapeHtml(row.test_type)}</td>
            <td>${patientLink}</td>
            <td>${this.escapeHtml(row.cedula)}</td>
            <td>${this.escapeHtml(row.result)}</td>
            <td>${this.escapeHtml(row.test_date)}</td>
            <td>${this.escapeHtml(row.created_by_name || '')}</td>
          </tr>
        `;
      }).join('') : `<tr><td colspan="7" class="text-center">${window.i18n_t ? window.i18n_t('no_data') : 'No data'}</td></tr>`;
      this.initDataTable();
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${this.escapeHtml(error.message)}</td></tr>`;
    }
  }

  static initDataTable() {
    if (!window.jQuery || !window.jQuery.fn.DataTable) return;
    if ($.fn.dataTable.isDataTable('#testsTable')) {
      $('#testsTable').DataTable().destroy();
      $('#testsTable').find('tbody').off('click');
    }

    this.dataTable = $('#testsTable').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      order: [[0, 'desc']],
      columnDefs: [{ orderable: false, targets: [2] }]
    });
  }
}

window.addEventListener('DOMContentLoaded', () => {
  TestsView.init();
});
