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
  static escapeAttr(s){ return (s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

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

  static dataTable = null;

  static initDataTable() {
    if (!window.jQuery || !window.jQuery.fn.DataTable) return;

    if ($.fn.dataTable.isDataTable('#patientsTable')) {
      $('#patientsTable').DataTable().destroy();
      $('#patientsTable').find('tbody').off('click');
    }

    this.dataTable = $('#patientsTable').DataTable({
      dom: 'Bfrtip',
      buttons: [
        { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
        { extend: 'print', exportOptions: { columns: ':not(:last-child)' } }
      ],
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      columnDefs: [{ orderable: false, targets: -1 }]
    });
  }

  static async initAllergyModal() {
    const modalEl = document.getElementById('allergyModal');
    if (!modalEl) return;

    this.allergyModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
    const form = document.getElementById('patientAllergyForm');
    if (form) {
      form.addEventListener('submit', (event) => this.saveAllergy(event));
    }
    await this.loadAllergyPatients();
  }

  static async loadAllergyPatients() {
    const select = document.getElementById('patient_id');
    if (!select) return;
    select.innerHTML = '<option value="">Cargando pacientes...</option>';
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      const patients = Array.isArray(json.data) ? json.data : [];
      select.innerHTML = '<option value="">Seleccione un paciente</option>' + patients.map(p => {
        const name = `${p.first_name || ''} ${p.last_name || ''}`.trim();
        return `<option value="${this.escapeAttr(p.id)}">${this.escapeHtml(name)}${p.cedula ? ' (' + this.escapeHtml(p.cedula) + ')' : ''}</option>`;
      }).join('');
    } catch (err) {
      select.innerHTML = '<option value="">No se pudieron cargar los pacientes</option>';
    }
  }

  static resetAllergyForm(patientId = '') {
    const errorBox = document.getElementById('patientAllergyError');
    if (errorBox) {
      errorBox.textContent = '';
      errorBox.classList.add('d-none');
    }
    document.getElementById('allergyId').value = '';
    document.getElementById('patient_id').value = patientId;
    document.getElementById('allergen').value = '';
    document.getElementById('reaction').value = '';
    document.getElementById('severity').value = '';
    document.getElementById('status').value = 'active';
    document.getElementById('noted_date').value = '';
    document.getElementById('notes').value = '';
    const title = document.getElementById('allergyModalLabel');
    if (title) title.textContent = 'Agregar alergia';
  }

  static async saveAllergy(event) {
    event.preventDefault();
    const errorBox = document.getElementById('patientAllergyError');
    if (errorBox) {
      errorBox.textContent = '';
      errorBox.classList.add('d-none');
    }

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
      if (this.allergyModal) this.allergyModal.hide();
    } catch (err) {
      if (errorBox) {
        errorBox.textContent = err.message || 'Save failed';
        errorBox.classList.remove('d-none');
      }
    }
  }

  static openAllergyModal(patientId) {
    window.location.href = '/alergias.php?patient_id=' + encodeURIComponent(patientId);
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
        this.initDataTable();
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
              <button class="btn btn-sm btn-info btn-allergies" data-id="${p.id}" title="Alergias"><i class="fa-solid fa-allergies"></i></button>
              <button class="btn btn-sm btn-primary btn-edit" data-id="${p.id}" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-sm btn-danger btn-del" data-id="${p.id}" title="Delete"><i class="fa-solid fa-trash"></i></button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      this.bindListEvents();
      this.initDataTable();

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

    if (!this._listClickHandlerBound) {
      this._listClickHandlerBound = this.listClickHandler.bind(this);
    }

    tableBody.removeEventListener('click', this._listClickHandlerBound);
    tableBody.addEventListener('click', this._listClickHandlerBound);
  }

  static async listClickHandler(e){
    const button = e.target.closest('button');
    if (!button) return;
    const id = button.dataset.id;
    if (!id) return;
    if (button.classList.contains('btn-allergies')) {
      this.openAllergyModal(id);
      return;
    }
    if (button.classList.contains('btn-edit')) {
      window.location.href = '/paciente.php?id=' + encodeURIComponent(id);
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
    const btnAllergiesPage = document.getElementById('btnAllergiesPage');
    if (btnAllergiesPage) {
      btnAllergiesPage.addEventListener('click', (event) => {
        event.preventDefault();
        window.location.href = '/alergias.php';
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
          window.location.href = '/pacientes.php';
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

class PatientsModal {
  static modalId = 'patientsListModal';
  static tableId = 'patientsModalSelectionTable';
  static messageId = 'patientsModalMessage';
  static bootstrapModal = null;
  static onSelect = null;

  static init() {
    const modalEl = document.getElementById(this.modalId);
    if (!modalEl) return;

    this.bootstrapModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: true });

    const body = modalEl.querySelector('tbody');
    if (body) {
      body.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-action="select-patient"]');
        if (!btn) return;

        const id = btn.dataset.patientId;
        const name = btn.dataset.patientName;
        this.hide();
        if (this.onSelect) {
          this.onSelect({ id: Number(id), name });
          this.onSelect = null;
        }

        const selectedEvent = new CustomEvent('patientSelected', { detail: { id: Number(id), name } });
        document.dispatchEvent(selectedEvent);
      });
    }

    modalEl.addEventListener('hidden.bs.modal', () => {
      this.onSelect = null;
    });
  }

  static async loadPatients() {
    const tableBody = document.querySelector(`#${this.tableId} tbody`);
    const messageBox = document.getElementById(this.messageId);
    if (!tableBody || !messageBox) return;

    messageBox.classList.add('d-none');
    messageBox.textContent = '';

    try {
      const result = await PatientsDataLayer.list();
      const rows = Array.isArray(result.data) ? result.data : [];
      tableBody.innerHTML = '';

      if (rows.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${window.i18n_t ? window.i18n_t('no_data') : 'No patients found'}</td></tr>`;
        return;
      }

      rows.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${p.id}</td>
          <td>${PatientsView.escapeHtml((p.first_name || '') + ' ' + (p.last_name || ''))}</td>
          <td>${PatientsView.escapeHtml(p.cedula || '')}</td>
          <td>${PatientsView.escapeHtml(p.dob || '')}</td>
          <td>${PatientsView.escapeHtml(p.email || '')}</td>
          <td>
            <button type="button" class="btn btn-sm btn-primary" data-action="select-patient" data-patient-id="${p.id}" data-patient-name="${PatientsView.escapeAttr((p.first_name || '') + ' ' + (p.last_name || ''))}">Select</button>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    } catch (err) {
      console.error('Failed to load patients for selection modal', err);
      tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${window.i18n_t ? window.i18n_t('error') : 'Error cargando pacientes'}</td></tr>`;
      if (messageBox) {
        messageBox.textContent = (err.message || 'Error cargando pacientes');
        messageBox.classList.remove('d-none');
      }
    }
  }

  static async show({ onSelect } = {}) {
    if (!this.bootstrapModal) this.init();
    this.onSelect = typeof onSelect === 'function' ? onSelect : null;
    await this.loadPatients();
    if (this.bootstrapModal) this.bootstrapModal.show();
  }

  static hide() {
    if (this.bootstrapModal) this.bootstrapModal.hide();
  }
}

window.PatientsModal = PatientsModal;

window.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('#patientsTable')) {
    PatientsView.initList();
  }
  if (document.querySelector('#patientForm')) {
    PatientsView.initForm();
  }
});
