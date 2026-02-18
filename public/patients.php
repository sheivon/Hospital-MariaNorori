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

<!-- Loading overlay -->
<div id="loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;justify-content:center;align-items:center;display:flex;">
    <div style="width:50px;height:50px;border:5px solid rgba(255,255,255,0.3);border-top:5px solid white;border-radius:50%;animation:spin 1s linear infinite;"></div>
</div>

<style>
@keyframes spin {0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>