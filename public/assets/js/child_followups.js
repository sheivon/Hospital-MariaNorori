class ChildFollowupDataLayer {
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
    return this.request('/api/child_followups_list.php');
  }

  static async create(payload) {
    return this.request('/api/child_followups_create.php', { method: 'POST', body: payload });
  }

  static async update(payload) {
    return this.request('/api/child_followups_update.php', { method: 'POST', body: payload });
  }

  static async delete(id) {
    const formData = new FormData();
    formData.append('id', id);
    return this.request('/api/child_followups_delete.php', { method: 'POST', body: formData });
  }

  static async listNotes(followupId) {
    return this.request('/api/child_followup_notes_list.php?seguimiento_id=' + encodeURIComponent(followupId));
  }

  static async createNote(payload) {
    return this.request('/api/child_followup_notes_create.php', { method: 'POST', body: payload });
  }
}

class ChildFollowupsView {
  static dataTable = null;
  static currentId = null;

  static t(key, fallback = key) {
    return window.i18n_t ? window.i18n_t(key) : fallback;
  }

  static escapeHtml(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  static async init() {
    if (!document.querySelector('#childFollowupsPage')) return;

    this.form = document.getElementById('childFollowupsForm');
    this.errorBox = document.getElementById('childFollowupsError');
    this.resetButton = document.getElementById('childFollowupsReset');
    this.openButton = document.getElementById('openChildFollowupModal');
    this.followupModal = document.getElementById('childFollowupModal');
    this.followupModalTitle = document.getElementById('childFollowupModalTitle');
    this.followupModalInstance = this.followupModal ? new bootstrap.Modal(this.followupModal) : null;
    this.notesModal = document.getElementById('childFollowupNotesModal');
    this.notesForm = document.getElementById('childFollowupNotesForm');
    this.notesErrorBox = document.getElementById('childFollowupNotesError');
    this.notesModalInstance = this.notesModal ? new bootstrap.Modal(this.notesModal) : null;

    await this.loadPatients();
    await this.loadList();

    if (this.form) {
      this.form.addEventListener('submit', (event) => this.submitForm(event));
    }
    if (this.resetButton) {
      this.resetButton.addEventListener('click', () => this.resetForm());
    }
    if (this.openButton) {
      this.openButton.addEventListener('click', () => this.openFollowupModal());
    }
    if (this.notesForm) {
      this.notesForm.addEventListener('submit', (event) => this.submitNoteForm(event));
    }
  }

  static async loadPatients() {
    const select = document.getElementById('patient_id');
    if (!select) return;
    select.innerHTML = `<option value="">${this.t('loading', 'Loading...')}</option>`;

    try {
      const response = await fetch('/api/patients_list.php?encountered=1', { credentials: 'same-origin' });
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const json = await response.json();
      const patients = Array.isArray(json.data) ? json.data : [];
      select.innerHTML = `<option value="">${this.t('select_patient', 'Select a patient')}</option>` + patients.map((p) => {
        const name = `${p.first_name || ''} ${p.last_name || ''}`.trim();
        return `<option value="${this.escapeHtml(p.id)}">${this.escapeHtml(name)}${p.cedula ? ' (' + this.escapeHtml(p.cedula) + ')' : ''}</option>`;
      }).join('');
    } catch (error) {
      select.innerHTML = `<option value="">${this.t('patient_load_failed', 'Unable to load patients')}</option>`;
      console.error('Error loading patients:', error);
    }
  }

  static async loadList() {
    const tableBody = document.querySelector('#childFollowupsTable tbody');
    if (!tableBody) return;

    try {
      const result = await ChildFollowupDataLayer.list();
      const rows = Array.isArray(result.data) ? result.data : [];
      if (!rows.length) {
        tableBody.innerHTML = '';
      } else {
        tableBody.innerHTML = rows.map((row) => `
          <tr>
            <td>${this.escapeHtml(row.id)}</td>
            <td>${this.escapeHtml((row.patient_first_name || '') + ' ' + (row.patient_last_name || ''))}</td>
            <td>${this.escapeHtml(row.cedula || '')}</td>
            <td>${this.escapeHtml(row.visit_date || '')}</td>
            <td>${this.escapeHtml(row.notas || '').slice(0, 120)}</td>
            <td>${this.escapeHtml(row.note_count || 0)}</td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-primary me-1" data-action="add-note" data-id="${this.escapeHtml(row.id)}">${this.t('note', 'Note')}</button>
              <button type="button" class="btn btn-sm btn-outline-success me-1" data-action="print" data-id="${this.escapeHtml(row.id)}">${this.t('print', 'Print')}</button>
              <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-action="edit" data-id="${this.escapeHtml(row.id)}">${this.t('edit', 'Edit')}</button>
              <button type="button" class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${this.escapeHtml(row.id)}">${this.t('delete', 'Delete')}</button>
            </td>
          </tr>
        `).join('');
      }

      this.initDataTable();
      this.bindActionButtons();
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${this.escapeHtml(error.message)}</td></tr>`;
    }
  }

  static initDataTable() {
    if (!window.jQuery || !window.jQuery.fn.DataTable) return;
    if ($.fn.dataTable.isDataTable('#childFollowupsTable')) {
      $('#childFollowupsTable').DataTable().destroy();
      $('#childFollowupsTable').find('tbody').off('click');
    }

    this.dataTable = $('#childFollowupsTable').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50],
      order: [[0, 'desc']],
      columnDefs: [{ orderable: false, targets: [4, 5, 6] }],
      language: {
        emptyTable: this.t('child_followups_no_records', 'No child follow-ups found.')
      }
    });
  }

  static bindActionButtons() {
    const table = document.getElementById('childFollowupsTable');
    if (!table) return;

    table.querySelectorAll('button[data-action="edit"]').forEach((button) => {
      button.addEventListener('click', () => this.editRecord(parseInt(button.dataset.id, 10)));
    });

    table.querySelectorAll('button[data-action="add-note"]').forEach((button) => {
      button.addEventListener('click', () => this.openNotesModal(parseInt(button.dataset.id, 10)));
    });

    table.querySelectorAll('button[data-action="delete"]').forEach((button) => {
      button.addEventListener('click', () => this.deleteRecord(parseInt(button.dataset.id, 10)));
    });

    table.querySelectorAll('button[data-action="print"]').forEach((button) => {
      button.addEventListener('click', () => this.printRecord(parseInt(button.dataset.id, 10)));
    });
  }

  static async editRecord(id) {
    if (!id) return;
    this.setError('');

    try {
      const response = await fetch(`/api/child_followups_list.php?patient_id=&id=${id}`, { credentials: 'same-origin' });
      const json = await response.json();

      if (!json.success) {
        throw new Error(json.error || this.t('followup_load_failed', 'Unable to load record'));
      }

      const record = (Array.isArray(json.data) ? json.data[0] : null);
      if (!record) {
        throw new Error(this.t('followup_not_found', 'Follow-up record not found'));
      }

      this.currentId = id;
      document.getElementById('followup_id').value = id;
      document.getElementById('patient_id').value = record.patient_id || '';
      document.getElementById('visit_date').value = record.visit_date || '';
      document.getElementById('respira_rapida').checked = record.respira_rapida === '1' || record.respira_rapida === 1;
      document.getElementById('dificultad_alimentarse').checked = record.dificultad_alimentarse === '1' || record.dificultad_alimentarse === 1;
      document.getElementById('dificultad_respirar').checked = record.dificultad_respirar === '1' || record.dificultad_respirar === 1;
      document.getElementById('convulsiones').checked = record.convulsiones === '1' || record.convulsiones === 1;
      document.getElementById('fiebre').checked = record.fiebre === '1' || record.fiebre === 1;
      document.getElementById('peso_g').value = record.peso_g || '';
      document.getElementById('talla_cm').value = record.talla_cm || '';
      document.getElementById('malnutricion').checked = record.malnutricion === '1' || record.malnutricion === 1;
      document.getElementById('vacuna').checked = record.vacuna === '1' || record.vacuna === 1;
      document.getElementById('vitamina_a').checked = record.vitamina_a === '1' || record.vitamina_a === 1;
      document.getElementById('hierro').checked = record.hierro === '1' || record.hierro === 1;
      document.getElementById('buen_trato').checked = record.buen_trato === '1' || record.buen_trato === 1;
      document.getElementById('relacion_afectivo').value = record.relacion_afectivo || '';
      document.getElementById('notas').value = record.notas || '';

      if (this.followupModalInstance) {
        if (this.followupModalTitle) {
          this.followupModalTitle.textContent = this.t('edit_child_followup_title', 'Edit follow-up');
        }
        this.followupModalInstance.show();
      }
    } catch (error) {
      this.setError(error.message || this.t('followup_load_failed', 'Unable to load record'));
    }
  }

  static async loadNotes() {
    const notesList = document.getElementById('childFollowupNotesList');
    if (!notesList || !this.notesFollowupId) return;

    notesList.innerHTML = `<li class="list-group-item text-center">${this.t('loading', 'Loading...')}</li>`;
    try {
      const result = await ChildFollowupDataLayer.listNotes(this.notesFollowupId);
      const notes = Array.isArray(result.data) ? result.data : [];
      if (!notes.length) {
        notesList.innerHTML = `<li class="list-group-item text-center">${this.t('child_followup_no_notes', 'No notes yet.')}</li>`;
        return;
      }

      notesList.innerHTML = notes.map((note) => `
        <li class="list-group-item">
          <div class="d-flex justify-content-between align-items-start">
            <strong>${this.escapeHtml(note.tipo || this.t('note', 'Note'))}</strong>
            <small class="text-muted">${this.escapeHtml(note.created_at || '')}</small>
          </div>
          <div class="mt-2">${this.escapeHtml(note.contenido || '')}</div>
        </li>
      `).join('');
    } catch (error) {
      notesList.innerHTML = `<li class="list-group-item text-danger">${this.escapeHtml(error.message)}</li>`;
    }
  }

  static async submitNoteForm(event) {
    event.preventDefault();
    if (!this.notesForm || !this.notesFollowupId) return;

    const formData = new FormData(this.notesForm);
    formData.append('seguimiento_id', String(this.notesFollowupId));
    const tipo = (formData.get('tipo') || '').toString().trim();
    const contenido = (formData.get('contenido') || '').toString().trim();

    if (!contenido) {
      this.setNotesError(this.t('note_content_required', 'Note content is required'));
      return;
    }

    try {
      await ChildFollowupDataLayer.createNote(formData);
      this.setNotesError(this.t('note_saved', 'Note saved successfully'), false);
      await this.loadNotes();
      await this.loadList();
      this.notesForm.reset();
    } catch (error) {
      this.setNotesError(error.message || this.t('note_save_failed', 'Unable to save the note'));
    }
  }

  static setNotesError(message, isError = true) {
    if (!this.notesErrorBox) return;
    this.notesErrorBox.textContent = message;
    if (!message) {
      this.notesErrorBox.classList.add('d-none');
      return;
    }
    this.notesErrorBox.classList.remove('d-none');
    this.notesErrorBox.classList.toggle('text-danger', isError);
    this.notesErrorBox.classList.toggle('text-success', !isError);
  }

  static async deleteRecord(id) {
    if (!id || !confirm(this.t('confirm_delete_followup', 'Delete this follow-up?'))) {
      return;
    }

    try {
      await ChildFollowupDataLayer.delete(id);
      this.resetForm();
      await this.loadList();
      this.setError(this.t('followup_deleted', 'Follow-up deleted successfully'), false);
    } catch (error) {
      this.setError(error.message || this.t('followup_delete_failed', 'Unable to delete record'));
    }
  }

  static printRecord(id) {
    if (!id) return;
    window.open(`/print_followup.php?id=${encodeURIComponent(id)}`, '_blank');
  }

  static async openNotesModal(id) {
    if (!id) return;
    this.notesFollowupId = id;
    this.notesForm?.reset();
    this.setNotesError('');
    const title = document.getElementById('childFollowupNotesModalTitle');
    if (title) {
      title.textContent = `${this.t('child_followup_notes_title', 'Follow-up Notes')} #${id}`;
    }

    await this.loadNotes();
    this.notesModalInstance?.show();
  }

  static async submitForm(event) {
    event.preventDefault();
    if (!this.form) return;

    const formData = new FormData(this.form);
    if (!formData.get('patient_id')) {
      this.setError(this.t('patient_required', 'Please select a patient'));
      return;
    }

    this.setError('');
    try {
      if (this.currentId) {
        await ChildFollowupDataLayer.update(formData);
      } else {
        await ChildFollowupDataLayer.create(formData);
      }
      this.resetForm();
      await this.loadList();
      if (this.followupModalInstance) {
        this.followupModalInstance.hide();
      }
      this.setError(this.t('followup_saved', 'Follow-up saved successfully'), false);
    } catch (error) {
      this.setError(error.message || this.t('followup_save_failed', 'Unable to save the follow-up'));
    }
  }

  static resetForm() {
    this.currentId = null;
    if (!this.form) return;
    this.form.reset();
    document.getElementById('followup_id').value = '';
    if (this.followupModalTitle) {
      this.followupModalTitle.textContent = this.t('add_child_followup', 'Add follow-up');
    }
    this.setError('');
  }

  static openFollowupModal() {
    this.resetForm();
    if (this.followupModalInstance) {
      this.followupModalInstance.show();
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
  ChildFollowupsView.init();
});
