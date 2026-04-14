<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container my-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 id="pageTitle" data-i18n="add_patient">Agregar paciente</h2>
    <a href="/pacientes.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i><span data-i18n="back">Atrás</span></a>
  </div>

  <div id="patientAlert" class="alert alert-danger d-none" role="alert"></div>
  <hr/>
  <form id="patientForm" class="row g-3">
    <input type="hidden" id="id" name="id">
  <div class="m-1">
    <div class="row mb-3">
    <div class="col-md-3">
      <label for="first_name" class="form-label" data-i18n="first_name">Nombre</label>
      <input type="text" class="form-control" id="first_name" name="first_name" required data-i18n-placeholder="first_name">
      <div class="invalid-feedback" id="firstNameError"></div>
    </div>
    <div class="col-md-3">
      <label for="last_name" class="form-label" data-i18n="last_name">Apellidos</label>
      <input type="text" class="form-control" id="last_name" name="last_name" required data-i18n-placeholder="last_name">
      <div class="invalid-feedback" id="lastNameError"></div>
    </div>

    <div class="col-md-2">
      <label for="cedula" class="form-label" data-i18n="cedula">Cédula</label>
      <input type="text" class="form-control" id="cedula" name="cedula" data-i18n-placeholder="cedula">
      <div class="invalid-feedback" id="cedulaError"></div>
    </div>
    <div class="col-md-2">
      <label for="dob" class="form-label" data-i18n="dob">Fecha de nacimiento</label>
      <input type="date" class="form-control" id="dob" name="dob" data-i18n-placeholder="dob">
    </div>

    <div class="col-md-2">
      <label for="gender" class="form-label" data-i18n="gender">Sexo</label>
      <select class="form-select" id="gender" name="gender">
        <option value="O" data-i18n="gender_other">Otro</option>
        <option value="M" data-i18n="gender_male">Masculino</option>
        <option value="F" data-i18n="gender_female">Femenino</option>
      </select>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
      <label for="email" class="form-label" data-i18n="email">Correo electrónico</label>
      <input type="email" class="form-control" id="email" name="email" data-i18n-placeholder="email">
      <div class="invalid-feedback" id="emailError"></div>
    </div>
    
    <div class="col-md-2">
      <label for="marital_status" class="form-label" data-i18n="marital_status">Estado civil</label>
      <select class="form-select" id="marital_status" name="marital_status">
        <option value="" selected disabled data-i18n="select_marital_status">Seleccione</option>
        <option value="Soltero" data-i18n="marital_single">Soltero</option>
        <option value="Casado" data-i18n="marital_married">Casado</option>
        <option value="Divorciado" data-i18n="marital_divorced">Divorciado</option>
        <option value="Viudo" data-i18n="marital_widowed">Viudo</option>
      </select>
    </div>

    <div class="col-md-2">
      <label for="phone" class="form-label" data-i18n="phone">Teléfono</label>
      <input type="text" class="form-control" id="phone" name="phone" data-i18n-placeholder="phone">
    </div>
 
    <div class="col-md-2">
      <label for="insurance_provider" class="form-label" data-i18n="insurance_provider">Aseguradora</label>
      <input type="text" class="form-control" id="insurance_provider" name="insurance_provider" data-i18n-placeholder="insurance_provider">
    </div>

    <div class="col-md-2">
      <label for="insurance_policy_no" class="form-label" data-i18n="insurance_policy_no">Seguro / No. INSS</label>
      <input type="text" class="form-control" id="insurance_policy_no" name="insurance_policy_no" data-i18n-placeholder="insurance_policy_no">
    </div>
 </div>

 <div class="row mb-3">
    <div class="col-md-3">
      <label for="expediente_no" class="form-label" data-i18n="expediente_no">No. Expediente</label>
      <input type="text" class="form-control" id="expediente_no" name="expediente_no" data-i18n-placeholder="expediente_no">
    </div>

    <div class="col-md-3">
      <label for="procedencia" class="form-label" data-i18n="procedencia">Procedencia</label>
      <input type="text" class="form-control" id="procedencia" name="procedencia" data-i18n-placeholder="procedencia">
    </div>

    <div class="col-md-3">
      <label for="father_name" class="form-label" data-i18n="father_name">Nombre del padre</label>
      <input type="text" class="form-control" id="father_name" name="father_name" data-i18n-placeholder="father_name">
    </div>

    <div class="col-md-3">
      <label for="mother_name" class="form-label" data-i18n="mother_name">Nombre de la madre</label>
      <input type="text" class="form-control" id="mother_name" name="mother_name" data-i18n-placeholder="mother_name">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
      <label for="education_level" class="form-label" data-i18n="education_level">Educación</label>
      <select class="form-select" id="education_level" name="education_level">
        <option value="" selected disabled data-i18n="select_education">Seleccione</option>
        <option value="Primaria" data-i18n="education_primary">Primaria</option>
        <option value="Secundaria" data-i18n="education_secondary">Secundaria</option>
        <option value="Universitaria" data-i18n="education_university">Universitaria</option>
        <option value="Otra" data-i18n="education_other">Otra</option>
      </select>
    </div>

    <div class="col-md-2">
      <label for="employer" class="form-label" data-i18n="employer">Empleador</label>
      <input type="text" class="form-control" id="employer" name="employer" data-i18n-placeholder="employer">
    </div>

    <div class="col-md-4">
      <label for="address" class="form-label" data-i18n="address">Dirección</label>
      <input type="text" class="form-control" id="address" name="address" data-i18n-placeholder="address">
    </div>
</div>
    <div class="col-12 mb-3">
      <label for="notes" class="form-label" data-i18n="notes">Notas</label>
      <textarea class="form-control" id="notes" name="notes" rows="3" data-i18n-placeholder="notes"></textarea>
    </div>

  </div>
  
<hr/>
    <div class="col-12 d-flex justify-content-end gap-2">
      <a href="/pacientes.php" class="btn btn-secondary" data-i18n="cancel">Cancelar</a>
      <button id="btnSave" class="btn btn-primary" type="submit"><i class="fa-solid fa-save me-1"></i><span data-i18n="save">Guardar</span></button>
    </div>
  </form>
</div>


<?php include __DIR__ . '/../templates/footer.php'; ?>