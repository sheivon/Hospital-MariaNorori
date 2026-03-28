class PatientsDataLayer {
  static async request(url, options = {}) {
    const response = await fetch(url, { credentials: 'same-origin', ...options });
    if (!response.ok) throw new Error('Network error');
    const json = await response.json();
    if (!json.success) {
      const err = new Error(json.error || 'API error');
      err.api = json;
      throw err;
    }
    return json;
  }

  static async list() {
    return PatientsDataLayer.request('/api/patients_list.php');
  }

  static async get(id) {
    return PatientsDataLayer.request('/api/patient_get.php?id=' + encodeURIComponent(id));
  }

  static async create(payload) {
    return PatientsDataLayer.request('/api/patients_create.php', { method: 'POST', body: payload });
  }

  static async update(payload) {
    return PatientsDataLayer.request('/api/patients_update.php', { method: 'POST', body: payload });
  }

  static async delete(id) {
    return PatientsDataLayer.request('/api/patients_delete.php', { method: 'POST', body: new URLSearchParams({ id }) });
  }

  static async checkCedula(cedula) {
    if (!cedula) return { available: true };
    const response = await fetch('/api/cedula_check.php?cedula=' + encodeURIComponent(cedula), { credentials: 'same-origin' });
    if (!response.ok) throw new Error('Network error');
    return await response.json();
  }
}

class PatientsView {
  static escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  static setError(message){
    const alertBox = document.getElementById('patientAlert');
    if (!alertBox) return;
    if (!message) {
      alertBox.classList.add('d-none');
      alertBox.textContent = '';
      return;
    }
    alertBox.classList.remove('d-none');
    alertBox.textContent = message;
  }

  static setFieldError(fieldId, msg){
    const input = document.getElementById(fieldId);
    const errorBox = document.getElementById(fieldId + 'Error');
    if (!input || !errorBox) return;
    if (!msg){
      input.classList.remove('is-invalid');
      errorBox.textContent = '';
      return;
    }
    input.classList.add('is-invalid');
    errorBox.textContent = msg;
  }

  static clearFieldErrors(){
    ['first_name','last_name','cedula','email'].forEach(id => this.setFieldError(id, ''));
  }

  static validateFirstName(){
    const v = document.getElementById('first_name').value.trim();
    this.setFieldError('first_name', v ? '' : (window.i18n_t ? window.i18n_t('first_name') : 'First Name') + ' is required');
  }

  static validateLastName(){
    const v = document.getElementById('last_name').value.trim();
    this.setFieldError('last_name', v ? '' : (window.i18n_t ? window.i18n_t('last_name') : 'Last Name') + ' is required');
  }

  static validateEmail(){
    const v = document.getElementById('email').value.trim();
    if (!v) { this.setFieldError('email', ''); return; }
    this.setFieldError('email', /^\S+@\S+\.\S+$/.test(v) ? '' : (window.i18n_t ? window.i18n_t('email') : 'Email') + ' is invalid');
  }

  static debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  }

  static async loadPatients() {
    const tableBody = document.querySelector('#patientsTable tbody');
    if (!tableBody) return;
    try {
      showLoadingOverlay();
      const result = await PatientsDataLayer.list();
      const rows = Array.isArray(result.data) ? result.data : [];
      tableBody.innerHTML = '';
      if (!rows.length) {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">${window.i18n_t ? window.i18n_t('no_data') : 'No data'}</td></tr>`;
        return;
      }

      rows.forEach((p) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${p.id}</td>
          <td>${this.escapeHtml(p.first_name + ' ' + p.last_name)}</td>
          <td>${this.escapeHtml(p.cedula||'')}</td>
          <td>${this.escapeHtml(p.expediente_no||'')}</td>
          <td>${p.dob||''}</td>
          <td>${this.escapeHtml(p.email||'')}</td>
          <td>${this.escapeHtml(p.phone||'')}</td>
          <td>
            <div class="btn-group d-flex" role="group">
              <button class="btn btn-sm btn-primary btn-edit" data-id="${p.id}"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-sm btn-danger btn-del" data-id="${p.id}"><i class="fa-solid fa-trash"></i></button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      this.bindListEvents();

    } catch (err) {
      tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">${window.i18n_t ? window.i18n_t('no_data') : 'No data'}</td></tr>`;
      console.error(err);
    } finally {
      hideLoadingOverlay();
    }
  }

  static bindListEvents(){
    const tableBody = document.querySelector('#patientsTable tbody');
    if (!tableBody) return;

    tableBody.removeEventListener('click', this.listClickHandler);
    tableBody.addEventListener('click', this.listClickHandler);
  }

  static async listClickHandler(e){
    const button = e.target.closest('button');
    if (!button) return;
    const id = button.dataset.id;
    if (!id) return;
    if (button.classList.contains('btn-edit')) {
      window.location.href = '/patient.php?id=' + encodeURIComponent(id);
      return;
    }
    if (button.classList.contains('btn-del')) {
      swal({
        title: window.i18n_t ? window.i18n_t('delete_confirm') : 'Confirm delete',
        text: '',
        icon: 'warning',
        buttons: [window.i18n_t ? window.i18n_t('cancel') : 'Cancel', window.i18n_t ? window.i18n_t('confirm_yes') : 'Yes'],
      }).then(async (confirmed) => {
        if (!confirmed) return;
        try {
          await PatientsDataLayer.delete(id);
          this.loadPatients();
        } catch (err) {
          swal({ text: err.message || (window.i18n_t ? window.i18n_t('error') : 'Error'), icon: 'error' });
        }
      });
    }
  }

  static async loadPatient(id){
    if (!id) return;
    try {
      showLoadingOverlay();
      const json = await PatientsDataLayer.get(id);
      const p = json.patient || {};
      const fields = ['id','first_name','last_name','cedula','dob','gender','phone','email','insurance_provider','insurance_policy_no','expediente_no','procedencia','father_name','mother_name','education_level','employer','address','notes','marital_status'];
      fields.forEach(f => {
        const el = document.getElementById(f);
        if (el) el.value = p[f] || '';
      });
      const title = document.getElementById('pageTitle');
      if (title) title.textContent = window.i18n_t ? window.i18n_t('edit_patient') : 'Edit Patient';
    } catch (err) {
      this.setError(window.i18n_t ? window.i18n_t('error') : 'Error');
    } finally {
      hideLoadingOverlay();
    }
  }

  static async initList(){
    await this.loadPatients();
    const btnPrintTable = document.getElementById('btnPrintTable');
    if (btnPrintTable) {
      btnPrintTable.addEventListener('click', () => {
        const table = document.getElementById('patientsTable');
        if (!table) return swal({ text: window.i18n_t ? window.i18n_t('no_table_to_print') : 'No table to print', icon:'info' });
        window.open('/print.php?resource=patients', '_blank');
      });
    }
  }

  static initForm(){
    const urlParams = new URLSearchParams(window.location.search);
    const patientId = urlParams.get('id');
    const form = document.getElementById('patientForm');
    if (!form) return;

    const validateCedulaRemote = this.debounce(async () => {
      const cedula = document.getElementById('cedula').value.trim();
      try {
        const data = await PatientsDataLayer.checkCedula(cedula);
        if (!data.success) {
          this.setFieldError('cedula', window.i18n_t ? window.i18n_t('error') : 'Error');
          return;
        }
        this.setFieldError('cedula', data.available ? '' : (window.i18n_t ? window.i18n_t('cedula_in_use') : 'Cedula already in use'));
      } catch (err) {
        this.setFieldError('cedula', window.i18n_t ? window.i18n_t('error') : 'Error');
      }
    }, 400);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.setError('');
      this.clearFieldErrors();

      const first = document.getElementById('first_name').value.trim();
      const last = document.getElementById('last_name').value.trim();
      const email = document.getElementById('email').value.trim();

      this.validateFirstName();
      this.validateLastName();
      this.validateEmail();

      if (!first || !last || (email && !/^\S+@\S+\.\S+$/.test(email))) {
        this.setError(window.i18n_t ? window.i18n_t('fix_errors') : 'Please fix the highlighted errors');
        return;
      }

      const formData = new FormData(form);
      try {
        const id = formData.get('id');
        const payload = id ? await PatientsDataLayer.update(formData) : await PatientsDataLayer.create(formData);
        if (payload.success) {
          window.location.href = '/patients.php';
        }
      } catch (err) {
        const errorMsg = err.message || (window.i18n_t ? window.i18n_t('error') : 'Error');
        if (errorMsg.toLowerCase().includes('cedula')) this.setFieldError('cedula', errorMsg);
        else if (errorMsg.toLowerCase().includes('email')) this.setFieldError('email', errorMsg);
        else this.setError(errorMsg);
      }
    });

    document.getElementById('first_name').addEventListener('input', () => this.validateFirstName());
    document.getElementById('last_name').addEventListener('input', () => this.validateLastName());
    document.getElementById('email').addEventListener('input', () => this.validateEmail());
    document.getElementById('cedula').addEventListener('input', validateCedulaRemote);

    if (patientId) this.loadPatient(patientId);
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('#patientsTable')) {
    PatientsView.initList();
  }
  if (document.querySelector('#patientForm')) {
    PatientsView.initForm();
  }
});
