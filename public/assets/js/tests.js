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
}

class TestsView {
  static dataTable = null;

  static escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  static async init() {
    if (!document.querySelector('#testsPage')) return;
    await this.loadTests();
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
