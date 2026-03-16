<!-- Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="testForm">
        <div class="modal-header">
          <h5 class="modal-title" id="testModalTitle" data-i18n="add_test">Add test</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="testFormError" class="alert alert-danger d-none"></div>
          <input type="hidden" name="id" id="testId">
          <div class="mb-3">
            <label class="form-label" data-i18n="patient">Patient ID</label>
            <input class="form-control" name="patient_id" id="test_patient_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="test_type">Test type</label>
            <input class="form-control" name="test_type" id="test_type" required>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="result">Result</label>
            <textarea class="form-control" name="result" id="result" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="test_date">Test date</label>
            <input type="datetime-local" class="form-control" name="test_date" id="test_date">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="notes">Notes</label>
            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="Close">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
