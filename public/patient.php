<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 id="pageTitle" data-i18n="add_patient">Add Patient</h2>
    <a href="/patients.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i><span data-i18n="back">Back</span></a>
  </div>

  <div id="patientAlert" class="alert alert-danger d-none" role="alert"></div>

  <form id="patientForm" class="row g-3">
    <input type="hidden" id="id" name="id">

    <div class="col-md-6">
      <label for="first_name" class="form-label" data-i18n="first_name">First Name</label>
      <input type="text" class="form-control" id="first_name" name="first_name" required data-i18n-placeholder="first_name">
      <div class="invalid-feedback" id="firstNameError"></div>
    </div>
    <div class="col-md-6">
      <label for="last_name" class="form-label" data-i18n="last_name">Last Name</label>
      <input type="text" class="form-control" id="last_name" name="last_name" required data-i18n-placeholder="last_name">
      <div class="invalid-feedback" id="lastNameError"></div>
    </div>

    <div class="col-md-6">
      <label for="cedula" class="form-label" data-i18n="cedula">Cedula</label>
      <input type="text" class="form-control" id="cedula" name="cedula" data-i18n-placeholder="cedula">
      <div class="invalid-feedback" id="cedulaError"></div>
    </div>
    <div class="col-md-6">
      <label for="dob" class="form-label" data-i18n="dob">Date of Birth</label>
      <input type="date" class="form-control" id="dob" name="dob" data-i18n-placeholder="dob">
    </div>

    <div class="col-md-6">
      <label for="gender" class="form-label" data-i18n="gender">Gender</label>
      <select class="form-select" id="gender" name="gender">
        <option value="O" data-i18n="gender_other">Other</option>
        <option value="M" data-i18n="gender_male">Male</option>
        <option value="F" data-i18n="gender_female">Female</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="marital_status" class="form-label" data-i18n="marital_status">Marital status</label>
      <select class="form-select" id="marital_status" name="marital_status">
        <option value="" selected disabled data-i18n="select">Select</option>
        <option value="single" data-i18n="marital_single">Single</option>
        <option value="married" data-i18n="marital_married">Married</option>
        <option value="divorced" data-i18n="marital_divorced">Divorced</option>
        <option value="widowed" data-i18n="marital_widowed">Widowed</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="phone" class="form-label" data-i18n="phone">Phone</label>
      <input type="text" class="form-control" id="phone" name="phone" data-i18n-placeholder="phone">
    </div>

    <div class="col-md-6">
      <label for="email" class="form-label" data-i18n="email">Email</label>
      <input type="email" class="form-control" id="email" name="email" data-i18n-placeholder="email">
      <div class="invalid-feedback" id="emailError"></div>
    </div>

    <div class="col-md-6">
      <label for="insurance_provider" class="form-label" data-i18n="insurance_provider">Insurance Provider</label>
      <input type="text" class="form-control" id="insurance_provider" name="insurance_provider" data-i18n-placeholder="insurance_provider">
    </div>

    <div class="col-md-6">
      <label for="insurance_policy_no" class="form-label" data-i18n="insurance_policy_no">Insurance / INSS No.</label>
      <input type="text" class="form-control" id="insurance_policy_no" name="insurance_policy_no" data-i18n-placeholder="insurance_policy_no">
    </div>

    <div class="col-md-6">
      <label for="expediente_no" class="form-label" data-i18n="expediente_no">Expediente No.</label>
      <input type="text" class="form-control" id="expediente_no" name="expediente_no" data-i18n-placeholder="expediente_no">
    </div>

    <div class="col-md-6">
      <label for="procedencia" class="form-label" data-i18n="procedencia">Procedencia</label>
      <input type="text" class="form-control" id="procedencia" name="procedencia" data-i18n-placeholder="procedencia">
    </div>

    <div class="col-md-6">
      <label for="father_name" class="form-label" data-i18n="father_name">Father's name</label>
      <input type="text" class="form-control" id="father_name" name="father_name" data-i18n-placeholder="father_name">
    </div>

    <div class="col-md-6">
      <label for="mother_name" class="form-label" data-i18n="mother_name">Mother's name</label>
      <input type="text" class="form-control" id="mother_name" name="mother_name" data-i18n-placeholder="mother_name">
    </div>

    <div class="col-md-6">
      <label for="education_level" class="form-label" data-i18n="education_level">Education</label>
      <input type="text" class="form-control" id="education_level" name="education_level" data-i18n-placeholder="education_level">
    </div>

    <div class="col-md-6">
      <label for="employer" class="form-label" data-i18n="employer">Employer</label>
      <input type="text" class="form-control" id="employer" name="employer" data-i18n-placeholder="employer">
    </div>

    <div class="col-md-6">
      <label for="address" class="form-label" data-i18n="address">Address</label>
      <input type="text" class="form-control" id="address" name="address" data-i18n-placeholder="address">
    </div>

    <div class="col-12">
      <label for="notes" class="form-label" data-i18n="notes">Notes</label>
      <textarea class="form-control" id="notes" name="notes" rows="3" data-i18n-placeholder="notes"></textarea>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/patients.php" class="btn btn-secondary" data-i18n="cancel">Cancel</a>
      <button id="btnSave" class="btn btn-primary" type="submit"><i class="fa-solid fa-save me-1"></i><span data-i18n="save">Save</span></button>
    </div>
  </form>
</div>


<?php include __DIR__ . '/../templates/footer.php'; ?>
