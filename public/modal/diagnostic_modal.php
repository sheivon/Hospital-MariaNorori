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
            <label class="form-label" data-i18n="diagnostics_description">Description</label>
            <textarea id="diagCrudDesc" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="diagnostics_cancel_btn">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
