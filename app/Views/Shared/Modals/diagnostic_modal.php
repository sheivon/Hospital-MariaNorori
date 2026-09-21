<!-- Create/Edit Diagnostic Modal -->
<div class="modal fade" id="diagCrudModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="diagCrudForm">
        <div class="modal-header">
          <h5 class="modal-title" data-i18n="diagnostics_add_btn">Add Diagnostic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="diagCrudError" class="alert alert-danger d-none"></div>
          <input type="hidden" id="diagCrudId">
          <div class="mb-2">
            <label class="form-label" data-i18n="patient">Patient</label>
            <select id="diagCrudPatient" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_type">Type</label>
            <input id="diagCrudType" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_date">Date</label>
            <input id="diagCrudDate" type="date" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_unit">Unit</label>
            <input id="diagCrudUnit" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_room">Room</label>
            <input id="diagCrudRoom" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_time">Time</label>
            <input id="diagCrudTime" type="time" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_inss">INSS</label>
            <input id="diagCrudInss" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_plan">Plan</label>
            <input id="diagCrudPlan" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_weight">Weight (kg)</label>
            <input id="diagCrudWeight" type="number" step="0.1" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_height">Height (cm)</label>
            <input id="diagCrudHeight" type="number" step="0.1" class="form-control">
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_description">Description</label>
            <textarea id="diagCrudDesc" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="cancel">Cancel</button>
          <button type="submit" class="btn btn-primary" data-i18n="save">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
