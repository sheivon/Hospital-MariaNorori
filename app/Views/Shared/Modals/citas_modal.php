<!-- Create/Edit Appointment Modal -->
<div class="modal fade" id="appointmentCrudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="appointmentCrudForm">

                <div class="modal-header">
                    <h5 class="modal-title" data-i18n="appointment_add_btn">
                        Add Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div id="appointmentCrudError" class="alert alert-danger d-none"></div>

                    <input type="hidden" id="appointmentCrudId">

                    <div class="mb-3">
                        <label class="form-label" data-i18n="patient">Patient</label>
                        <select id="appointmentCrudPatient" class="form-select" required></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="provider">Provider</label>
                        <select id="appointmentCrudProvider" class="form-select">
                            <option value="">-- Select Provider --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="encounter">Encounter</label>
                        <select id="appointmentCrudEncounter" class="form-select">
                            <option value="">-- None --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="appointment_datetime">
                            Appointment Date & Time
                        </label>
                        <input
                            id="appointmentCrudDateTime"
                            type="datetime-local"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="appointment_reason">
                            Reason
                        </label>
                        <input
                            id="appointmentCrudReason"
                            type="text"
                            class="form-control"
                            maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="appointment_status">
                            Status
                        </label>
                        <select id="appointmentCrudStatus" class="form-select">
                            <option value="scheduled" data-i18n="status_scheduled">Scheduled</option>
                            <option value="confirmed" data-i18n="status_confirmed">Confirmed</option>
                            <option value="completed" data-i18n="status_completed">Completed</option>
                            <option value="cancelled" data-i18n="status_cancelled">Cancelled</option>
                            <option value="no_show" data-i18n="status_no_show">No Show</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" data-i18n="appointment_notes">
                            Notes
                        </label>
                        <textarea
                            id="appointmentCrudNotes"
                            class="form-control"
                            rows="4"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        data-i18n="cancel">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-i18n="save">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>