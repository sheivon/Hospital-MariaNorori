<!-- Patient Modal -->
<div class="modal fade" id="patientsListModal" tabindex="-1" aria-labelledby="patientListModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="patientListModalLabel" data-i18n="patient_details_title">Select Patient</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="patientsModalMessage" class="alert d-none" role="alert"></div>
        <div class="table-responsive">
          <table class="table table-hover table-sm" id="patientsModalSelectionTable">
            <thead>
              <tr>
                <th>ID</th>
                <th data-i18n="first_name">Name</th>
                <th>Cédula</th>
                <th data-i18n="dob">DOB</th>
                <th>Email</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="6" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
