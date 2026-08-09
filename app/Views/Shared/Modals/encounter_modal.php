<div class="modal fade" id="encounterModal" tabindex="-1" aria-labelledby="encounterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="encounterModalLabel"><span data-i18n="add_encounter">Add Encounter</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="encAddAlert" class="alert alert-danger d-none" role="alert"></div>

        <form id="encAddForm" class="row g-3">
          <div class="col-md-4">
            <label for="encAddPatient" class="form-label" data-i18n="patient">Patient</label>
            <div class="input-group">
              <select id="encAddPatient" name="patient_id" class="form-select" required></select>
              <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#patientsListModal" data-i18n="search_patient">Buscar paciente</button>
            </div>
          </div>

          <div class="col-md-4">
            <label for="encAddDate" class="form-label" data-i18n="encounters_date">Date</label>
            <input type="datetime-local" class="form-control" id="encAddDate" name="encounter_date" required>
          </div>

          <div class="col-md-4">
            <label for="encAddType" class="form-label" data-i18n="encounters_type">Type</label>
            <select class="form-select" id="encAddType" name="encounter_type" required>
              <option value="" data-i18n="select_type">Select type</option>
              <option value="outpatient" data-i18n="encounter_type_outpatient">Outpatient</option>
              <option value="inpatient" data-i18n="encounter_type_inpatient">Inpatient</option>
              <option value="emergency" data-i18n="encounter_type_emergency">Emergency</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="encAddTriage" class="form-label" data-i18n="triage_level">Triage Level</label>
            <select id="encAddTriage" name="triage_level" class="form-select">
              <option value="" data-i18n="select_triage">Select triage</option>
              <option value="low" data-i18n="triage_low">Low</option>
              <option value="medium" data-i18n="triage_medium">Medium</option>
              <option value="high" data-i18n="triage_high">High</option>
              <option value="urgent" data-i18n="triage_urgent">Urgent</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="encAddStatus" class="form-label" data-i18n="encounters_status">Status</label>
            <select id="encAddStatus" name="status" class="form-select">
              <option value="open" data-i18n="status_open">Open</option>
              <option value="closed" data-i18n="status_closed">Closed</option>
            </select>
          </div>

          <div class="col-md-6">
            <label for="encAddDoctor" class="form-label" data-i18n="encounters_doctor">Doctor</label>
            <select id="encAddDoctor" name="attending_user_id" class="form-select"></select>
          </div>

          <div class="col-md-6">
            <label for="encAddReason" class="form-label" data-i18n="encounters_reason">Reason</label>
            <textarea class="form-control" id="encAddReason" name="reason_for_visit" rows="3"></textarea>
          </div>

          <div class="col-md-6">
            <label for="encAddNotes" class="form-label" data-i18n="notes">Notes</label>
            <textarea class="form-control" id="encAddNotes" name="notes" rows="3"></textarea>
          </div>

          {{-- Emergency-only fields: shown when encounter_type=emergency --}}
          <div class="col-12" id="emergencyFields" style="display:none;">
            <hr>
            <h6 class="mb-3" data-i18n="emergency_extra_fields">Emergency intake</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label for="encAddAdmissionDate" class="form-label" data-i18n="admission_date">Admission date</label>
                <input type="date" class="form-control" id="encAddAdmissionDate" name="admission_date">
              </div>
              <div class="col-md-4">
                <label for="encAddDischargeDate" class="form-label" data-i18n="discharge_date">Discharge date</label>
                <input type="date" class="form-control" id="encAddDischargeDate" name="discharge_date">
              </div>
              <div class="col-md-4">
                <label for="encAddEmergencyStatus" class="form-label" data-i18n="emergency_status">Emergency status</label>
                <select id="encAddEmergencyStatus" name="emergency_status" class="form-select">
                  <option value="" data-i18n="select">-- Select --</option>
                  <option value="Activo" data-i18n="emergency_status_active">Active</option>
                  <option value="Alta" data-i18n="emergency_status_discharged">Discharged</option>
                  <option value="Traslado" data-i18n="emergency_status_transferred">Transferred</option>
                  <option value="Fallecido" data-i18n="emergency_status_deceased">Deceased</option>
                </select>
              </div>
              <div class="col-md-12">
                <label for="encAddFormData" class="form-label" data-i18n="emergency_form_data">Additional emergency form data (JSON, optional)</label>
                <textarea class="form-control" id="encAddFormData" name="form_data" rows="4" placeholder='{"admission_service":"…","admission_diagnosis":"…"}'></textarea>
                <small class="form-text text-muted" data-i18n="emergency_form_data_help">Free-form JSON payload for any extra emergency fields (admission service, diagnosis, treating doctor, etc.).</small>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
            <button id="btnSaveEncAdd" class="btn btn-primary" type="submit">
              <i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
