class AppointmentsDataLayer {

    static async request(url, options = {}) {
        let response;
        try {
            response = await fetch(url, {
                credentials: "same-origin",
                ...options
            });
        } catch (networkErr) {
            throw new Error("Network error: " + (networkErr.message || "unreachable"));
        }

        const contentType = response.headers.get("content-type") || "";
        const rawText = await response.text();

        // If the response isn't JSON, surface the actual server text instead of letting
        // response.json() throw a vague "unexpected character" parse error.
        if (contentType.indexOf("application/json") === -1) {
            const snippet = rawText.replace(/\s+/g, " ").slice(0, 200);
            throw new Error(
                (response.ok ? "" : "HTTP " + response.status + " ") +
                "Expected JSON from " + url + " but got '" + (contentType || "unknown") +
                "'. First bytes: " + snippet
            );
        }

        let json;
        try {
            json = JSON.parse(rawText);
        } catch (parseErr) {
            throw new Error("Invalid JSON from " + url + ": " + parseErr.message);
        }

        if (!response.ok) {
            throw new Error("HTTP " + response.status + (json && json.error ? ": " + json.error : ""));
        }

        if (!json || json.success === false) {
            const err = new Error((json && json.error) || "API error");
            err.api = json;
            throw err;
        }

        return json;
    }

    static async list() {
        return this.request("/api/appointments_list.php");
    }

    static async get(id) {
        return this.request("/api/appointments_get.php?id=" + encodeURIComponent(id));
    }

    static async create(payload) {
        return this.request("/api/appointments_create.php", {
            method: "POST",
            body: payload
        });
    }

    static async update(payload) {
        return this.request("/api/appointments_update.php", {
            method: "POST",
            body: payload
        });
    }

    static async delete(id) {
        return this.request("/api/appointments_delete.php", {
            method: "POST",
            body: new URLSearchParams({ id })
        });
    }
}

class AppointmentsView {

    static dataTable = null;
    static modalInstance = null;

    static escapeHtml(str = "") {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }

    static setError(message) {
        const alert = document.getElementById("appointmentListAlert");
        if (!alert) return;

        if (!message) {
            alert.classList.add("d-none");
            alert.textContent = "";
            return;
        }
        alert.classList.remove("d-none");
        alert.textContent = message;
    }

    static getModal() {
        if (!this.modalInstance) {
            const el = document.getElementById('appointmentCrudModal');
            if (el && typeof bootstrap !== 'undefined') {
                this.modalInstance = new bootstrap.Modal(el);
                // After the modal finishes closing (covers save→close,
                // cancel→close, X-button close, and backdrop click):
                //   1. Refresh the appointments table so the latest CRUD
                //      changes are visible.
                //   2. Defensively remove any leftover backdrop / body
                //      lock — Bootstrap 5 sometimes leaves them behind
                //      when an async submit handler fires `hide()`.
                el.addEventListener('hidden.bs.modal', () => {
                    this.refreshTable();

                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    if (document.body.classList.contains('modal-open')) {
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');
                    }
                });
            }
        }
        return this.modalInstance;
    }

    /**
     * Re-fetch the appointments list and re-render the DataTable.
     * Safe to call repeatedly — destroys any existing DataTable first.
     */
    static async refreshTable() {
        // Only refresh if the table is on this page
        if (!document.querySelector("#appointmentsTable")) return;
        await this.loadAppointments();
    }

    /**
     * Populate the Patient <select> in the appointment modal with every available
     * patient. Reuses PatientsDataLayer.list() (loaded from patients.js).
     * Safe to call multiple times — re-renders the option list each time.
     * Returns a promise that resolves when options are populated (or fails
     * gracefully so the caller can still proceed).
     */
    static async loadPatientsForSelect() {
        const select = document.getElementById('appointmentCrudPatient');
        if (!select) return;

        const placeholder = window.i18n_t
            ? window.i18n_t('select_patient')
            : '-- Select Patient --';

        select.innerHTML = `<option value="" data-i18n="select_patient">${placeholder}</option>`;

        try {
            if (typeof PatientsDataLayer === 'undefined') {
                throw new Error('PatientsDataLayer is not loaded');
            }
            const result = await PatientsDataLayer.list();
            const patients = Array.isArray(result && result.data) ? result.data : [];

            // Sort alphabetically by last name, then first name
            patients.sort((a, b) => {
                const an = `${a.last_name || ''} ${a.first_name || ''}`.toLowerCase();
                const bn = `${b.last_name || ''} ${b.first_name || ''}`.toLowerCase();
                return an.localeCompare(bn);
            });

            const optionsHtml = patients
                .filter(p => p && p.id != null)
                .map(p => {
                    const name = `${p.first_name || ''} ${p.last_name || ''}`.trim() || ('#' + p.id);
                    const cedula = p.cedula ? ` (${this.escapeHtml(p.cedula)})` : '';
                    return `<option value="${this.escapeHtml(p.id)}">${this.escapeHtml(name)}${cedula}</option>`;
                })
                .join('');

            select.innerHTML = `<option value="" data-i18n="select_patient">${placeholder}</option>${optionsHtml}`;
        } catch (err) {
            console.error('Failed to load patients for appointment select:', err);
            const failedMsg = window.i18n_t
                ? window.i18n_t('patient_load_failed')
                : 'Failed to load patients';
            select.innerHTML = `<option value="" data-i18n="select_patient">${failedMsg}</option>`;
        }
    }

    /**
     * Populate the Provider <select> in the appointment modal with every available
     * medic (user with role='doctor'). Fetches /api/users_list.php?role=doctor.
     * Safe to call multiple times — re-renders the option list each time.
     */
    static async loadProvidersForSelect() {
        const select = document.getElementById('appointmentCrudProvider');
        if (!select) return;

        const placeholder = window.i18n_t
            ? window.i18n_t('select_provider')
            : '-- Select Provider --';

        select.innerHTML = `<option value="" data-i18n="select_provider">${placeholder}</option>`;

        try {
            const response = await fetch('/api/users_list.php?role=doctor', {
                credentials: 'same-origin'
            });
            const rawText = await response.text();
            const contentType = response.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') === -1) {
                throw new Error('Expected JSON from users_list.php, got ' + (contentType || 'unknown'));
            }
            const json = JSON.parse(rawText);
            if (!response.ok || !json || json.success === false) {
                throw new Error((json && json.error) || ('HTTP ' + response.status));
            }

            const providers = Array.isArray(json.data) ? json.data : [];

            // Sort alphabetically by fullname, then username
            providers.sort((a, b) => {
                const an = (a.fullname || a.username || '').toLowerCase();
                const bn = (b.fullname || b.username || '').toLowerCase();
                return an.localeCompare(bn);
            });

            const optionsHtml = providers
                .filter(u => u && u.id != null)
                .map(u => {
                    const name = (u.fullname || u.username || ('#' + u.id)).trim();
                    const roleSuffix = u.role ? ` — ${this.escapeHtml(u.role)}` : '';
                    return `<option value="${this.escapeHtml(u.id)}">${this.escapeHtml(name)}${roleSuffix}</option>`;
                })
                .join('');

            select.innerHTML = `<option value="" data-i18n="select_provider">${placeholder}</option>${optionsHtml}`;
        } catch (err) {
            console.error('Failed to load providers for appointment select:', err);
            const failedMsg = window.i18n_t
                ? window.i18n_t('patient_load_failed')
                : 'Failed to load providers';
            select.innerHTML = `<option value="" data-i18n="select_provider">${failedMsg}</option>`;
        }
    }

    static initDataTable() {
        if (!window.jQuery || !$.fn.DataTable) return;

        if ($.fn.dataTable.isDataTable("#appointmentsTable")) {
            $("#appointmentsTable").DataTable().destroy();
        }

        this.dataTable = $("#appointmentsTable").DataTable({
            responsive: true,
            dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [{
                orderable: false,
                targets: -1
            }]
        });
    }

    static async loadAppointments() {
        const tbody = document.querySelector("#appointmentsTable tbody");
        if (!tbody) return;

        try {
            if (typeof showLoadingOverlay === 'function') showLoadingOverlay();

            const result = await AppointmentsDataLayer.list();
            const rows = Array.isArray(result.data) ? result.data : [];

            tbody.innerHTML = "";

            if (!rows.length) {
                // Render an empty state row that matches the 7-column structure
                // so DataTables doesn't throw "Incorrect column count" (tn/18).
                tbody.innerHTML = `
                    <tr class="no-appointments-row">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="text-center">
                            ${window.i18n_t ? window.i18n_t('no_appointments_found') : 'No appointments found'}
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                `;
                this.initDataTable();
                return;
            }

            rows.forEach(a => {
                if (!a || a.id == null) return; // skip malformed rows
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${this.escapeHtml(a.id)}</td>
                    <td>${this.escapeHtml(a.patient_name || "")}</td>
                    <td>${this.escapeHtml(a.provider_name || "")}</td>
                    <td>${this.escapeHtml(a.appointment_at || "")}</td>
                    <td>${this.escapeHtml(a.reason || "")}</td>
                    <td>${this.escapeHtml(a.status || "")}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm btn-edit" data-id="${this.escapeHtml(a.id)}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${this.escapeHtml(a.id)}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            this.bindEvents();
            this.initDataTable();

        } catch (err) {
            console.error(err);
            this.setError(err.message);
        } finally {
            if (typeof hideLoadingOverlay === 'function') hideLoadingOverlay();
        }
    }

    static bindEvents() {
        const tbody = document.querySelector("#appointmentsTable tbody");
        if (!tbody) return;

        tbody.onclick = (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;

            const id = btn.dataset.id;

            if (btn.classList.contains("btn-edit")) {
                // FIXED: Open modal inline instead of redirecting
                this.openEditModal(id);
            }

            if (btn.classList.contains("btn-delete")) {
                if (typeof swal === 'function') {
                    swal({
                        title: window.i18n_t ? window.i18n_t('delete_appointment') : 'Delete appointment?',
                        icon: "warning",
                        buttons: true,
                        dangerMode: true
                    }).then(async ok => {
                        if (!ok) return;
                        try {
                            await AppointmentsDataLayer.delete(id);
                            this.loadAppointments();
                        } catch (err) {
                            swal(err.message, "", "error");
                        }
                    });
                } else {
                    if (confirm(window.i18n_t ? window.i18n_t('delete_appointment') : 'Delete appointment?')) {
                        AppointmentsDataLayer.delete(id)
                            .then(() => this.loadAppointments())
                            .catch(err => alert(err.message));
                    }
                }
            }
        };
    }

    static async loadAppointment(id) {
        try {
            if (typeof showLoadingOverlay === 'function') showLoadingOverlay();
            const result = await AppointmentsDataLayer.get(id);
            if (!result || !result.appointment) return null;
            const a = result.appointment;
            if (!a || a.id == null) return null;

            document.getElementById("appointmentCrudId").value = a.id;
            document.getElementById("appointmentCrudPatient").value = a.patient_id || "";
            document.getElementById("appointmentCrudProvider").value = a.provider_user_id || "";
            document.getElementById("appointmentCrudEncounter").value = a.encounter_id || "";
            document.getElementById("appointmentCrudDateTime").value = a.appointment_at || "";
            document.getElementById("appointmentCrudReason").value = a.reason || "";
            document.getElementById("appointmentCrudStatus").value = a.status || "scheduled";
            document.getElementById("appointmentCrudNotes").value = a.notes || "";
            return a;
        } catch (err) {
            console.error(err);
            return null;
        } finally {
            if (typeof hideLoadingOverlay === 'function') hideLoadingOverlay();
        }
    }

    static openCreateModal() {
        const form = document.getElementById('appointmentCrudForm');
        if (form) form.reset();

        const idField = document.getElementById('appointmentCrudId');
        if (idField) idField.value = '';

        const title = document.querySelector('#appointmentCrudModal .modal-title');
        if (title) title.textContent = window.i18n_t ? window.i18n_t('add_appointment') : 'Add Appointment';

        // Clear any previous error
        const errorDiv = document.getElementById("appointmentCrudError");
        if (errorDiv) {
            errorDiv.classList.add("d-none");
            errorDiv.textContent = "";
        }

        // Refresh the patient and provider lists so newly added entries appear
        this.loadPatientsForSelect();
        this.loadProvidersForSelect();

        this.getModal()?.show();
    }

    static async openEditModal(id) {
        const errorDiv = document.getElementById("appointmentCrudError");
        try {
            if (typeof showLoadingOverlay === 'function') showLoadingOverlay();

            const result = await AppointmentsDataLayer.get(id);
            if (!result || !result.appointment) {
                if (errorDiv) {
                    errorDiv.textContent = window.i18n_t ? window.i18n_t('error') : 'Appointment not found';
                    errorDiv.classList.remove("d-none");
                }
                return;
            }
            const a = result.appointment;
            if (!a || a.id == null) {
                if (errorDiv) {
                    errorDiv.textContent = window.i18n_t ? window.i18n_t('error') : 'Appointment not found';
                    errorDiv.classList.remove("d-none");
                }
                return;
            }

            const form = document.getElementById('appointmentCrudForm');
            if (form) form.reset();

            // Ensure the patient and provider <select>s have options before assigning values
            await this.loadPatientsForSelect();
            await this.loadProvidersForSelect();

            document.getElementById('appointmentCrudId').value = a.id;
            document.getElementById('appointmentCrudPatient').value = a.patient_id || '';
            document.getElementById('appointmentCrudProvider').value = a.provider_user_id || '';
            document.getElementById('appointmentCrudEncounter').value = a.encounter_id || '';
            document.getElementById('appointmentCrudDateTime').value = a.appointment_at || '';
            document.getElementById('appointmentCrudReason').value = a.reason || '';
            document.getElementById('appointmentCrudStatus').value = a.status || 'scheduled';
            document.getElementById('appointmentCrudNotes').value = a.notes || '';

            const title = document.querySelector('#appointmentCrudModal .modal-title');
            if (title) title.textContent = window.i18n_t ? window.i18n_t('edit_appointment') : 'Edit Appointment';

            if (errorDiv) {
                errorDiv.classList.add("d-none");
                errorDiv.textContent = "";
            }

            this.getModal()?.show();
        } catch (err) {
            console.error(err);
            if (errorDiv) {
                errorDiv.textContent = err.message || (window.i18n_t ? window.i18n_t('error') : 'Error');
                errorDiv.classList.remove("d-none");
            } else {
                alert(err.message);
            }
        } finally {
            if (typeof hideLoadingOverlay === 'function') hideLoadingOverlay();
        }
    }

    static initForm() {
        const form = document.getElementById("appointmentCrudForm");
        if (!form) return;

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const data = new FormData(form);
            const id = data.get("id");

            // Clear previous error
            const errorDiv = document.getElementById("appointmentCrudError");
            if (errorDiv) {
                errorDiv.classList.add("d-none");
                errorDiv.textContent = "";
            }

            try {
                if (id) {
                    await AppointmentsDataLayer.update(data);
                } else {
                    await AppointmentsDataLayer.create(data);
                }

                // Close modal — the `hidden.bs.modal` listener attached in
                // getModal() will refresh the DataTable and clean up any
                // leftover backdrop once the modal finishes tearing down.
                this.getModal()?.hide();
                form.reset();

            } catch (err) {
                console.error(err);
                if (errorDiv) {
                    errorDiv.textContent = err.message || (window.i18n_t ? window.i18n_t('save_failed') : 'Save failed');
                    errorDiv.classList.remove("d-none");
                } else {
                    alert(err.message);
                }
            }
        });
    }

    static initList() {
        this.loadAppointments();
    }
}

window.addEventListener("DOMContentLoaded", () => {

    if (document.querySelector("#appointmentsTable")) {
        AppointmentsView.initList();
    }

    if (document.querySelector("#appointmentCrudForm")) {
        AppointmentsView.initForm();
        // Pre-populate the patient and provider selects on page load so the modal is ready instantly.
        AppointmentsView.loadPatientsForSelect();
        AppointmentsView.loadProvidersForSelect();
    }

    const newBtn = document.getElementById("btnOpenAppointmentModal");
    if (newBtn) {
        newBtn.addEventListener("click", (e) => {
            // Let Bootstrap open the modal via data-bs-toggle, but reset state first.
            e.preventDefault();
            AppointmentsView.openCreateModal();
        });
    }

});