<?php $GLOBALS['PAGE_SCRIPTS'][] = 'datatables'; $GLOBALS['PAGE_SCRIPTS'][] = 'patients'; ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0"><i class="fa-solid fa-users me-2"></i><span data-i18n="patients">Pacientes</span></h2>
      <p class="text-muted mb-0" data-i18n="patients_description">Administrar registros de pacientes.</p>
    </div>
    <div class="d-flex gap-2">
      <button id="btnAllergiesPage" type="button" class="btn btn-outline-warning" title="Alergias">
        <i class="fa-solid fa-allergies me-1"></i><span data-i18n="allergies">Alergias</span>
      </button>
      <button id="btnAddPatient" type="button" class="btn btn-success">
        <i class="fa-solid fa-user-plus me-1"></i><span data-i18n="add_patient">Agregar paciente</span>
      </button>
    </div>
  </div>

  <div id="patientAlert" class="alert alert-danger d-none" role="alert"></div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="patientsTable" class="table table-bordered table-striped align-middle w-100 dt-container">
          <thead>
            <tr>
              <th style="width:5%;">ID</th>
              <th data-i18n="label_fullname">Nombre completo</th>
              <th data-i18n="cedula">Cédula</th>
              <th data-i18n="expediente_no">No. Expediente</th>
              <th data-i18n="dob">Fecha de nacimiento</th>
              <th data-i18n="email">Correo electrónico</th>
              <th data-i18n="phone">Teléfono</th>
              <th style="width:15%;" data-i18n="actions">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="text-center text-muted" data-i18n="loading">Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include APP_ROOT . '/app/Views/Shared/Modals/patient_modal.php'; ?>
