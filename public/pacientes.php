<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?> 
<div class="card container mt-4 p-0">
  <div class="card-head d-flex justify-content-between align-items-center mb-3 bg-success text-white rounded-top">
    <h3 data-i18n="patients">Pacientes</h3>
    <div>
      <a href="/alergias.php" class="btn btn-secondary me-2" id="btnAllergiesPage"><i class="fa-solid fa-allergies me-1"></i><span data-i18n="view_allergies">Ver alergias</span></a>
      <a href="/paciente.php" class="btn btn-success me-2"><i class="fa-solid fa-user-plus me-1"></i><span data-i18n="add_patient">Agregar paciente</span></a>
    </div>
  </div>
  <div class="card-body">
    <table id="patientsTable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th data-i18n="first_name">Nombre</th>
          <th>Cédula</th>
          <th>Expediente</th>
          <th data-i18n="dob">Fecha de nacimiento</th>
          <th>Correo</th>
          <th>Teléfono</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <!-- rows inserted via JS -->
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/modal/patient_allergy_modal.php'; ?>
<?php include __DIR__ . '/../templates/loading_overlay.php'; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>