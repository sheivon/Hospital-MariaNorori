<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?> 
<div class="card container mt-4 p-0">
  <div class="card-head d-flex justify-content-between align-items-center mb-3 bg-success text-white rounded-top px-1">
    <h3 data-i18n="patients">Patients</h3>
    <div>
      <button class="btn btn-secondary me-2" id="btnPrintTable"><i class="fa-solid fa-print me-1"></i><span data-i18n="print">Print</span></button>
      <button class="btn btn-success me-2" id="btnAdd"><i class="fa-solid fa-user-plus me-1"></i><span data-i18n="add_patient">Add patient</span></button>
    </div>
  </div>
  <div class="card-body px-1">
    <table id="patientsTable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Cédula</th>
          <th>DOB</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- rows inserted via JS -->
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../templates/loading_overlay.php'; ?>

<?php include __DIR__ . '/modal/patient_modal.php'; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>
