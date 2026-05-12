<!-- Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="patientModalLabel" data-i18n="patient_details_title">Patient Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="patientForm">
          <input type="hidden" id="id">
          <input type="hidden" id="patientId" name="id">
          <div class="mb-3">
            <label for="first_name" class="form-label" data-i18n="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required data-i18n-placeholder="first_name">
          </div>
          <div class="mb-3">
            <label for="last_name" class="form-label" data-i18n="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required data-i18n-placeholder="last_name">
          </div>
          <div class="mb-3">
            <label for="cedula" class="form-label" data-i18n="cedula">Cedula</label>
            <input type="text" class="form-control" id="cedula" name="cedula" data-i18n-placeholder="cedula">
          </div>
          <div class="mb-3">
            <label for="dob" class="form-label" data-i18n="dob">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob" data-i18n-placeholder="dob">
          </div>
          <div class="mb-3">
            <label for="gender" class="form-label" data-i18n="gender">Gender</label>
            <select class="form-select" id="gender" name="gender">
              <option value="O" data-i18n="gender_other">Other</option>
              <option value="M" data-i18n="gender_male">Male</option>
              <option value="F" data-i18n="gender_female">Female</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label" data-i18n="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" data-i18n-placeholder="email">
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
