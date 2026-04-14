<!-- Patient Allergy Modal -->
<div class="modal fade" id="allergyModal" tabindex="-1" aria-labelledby="allergyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="patientAllergyForm">
        <div class="modal-header">
          <h5 class="modal-title" id="allergyModalLabel">Agregar alergia</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="patientAllergyError" class="alert alert-danger d-none" role="alert"></div>
          <input type="hidden" id="allergyId">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="patient_id" class="form-label">Paciente</label>
              <select id="patient_id" name="patient_id" class="form-select" required>
                <option value="">Seleccione un paciente</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Estado</label>
              <select id="status" name="status" class="form-select">
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="allergen" class="form-label">Alérgeno</label>
              <input type="text" id="allergen" name="allergen" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="severity" class="form-label">Severidad</label>
              <input type="text" id="severity" name="severity" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="reaction" class="form-label">Reacción</label>
              <input type="text" id="reaction" name="reaction" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="noted_date" class="form-label">Fecha</label>
              <input type="date" id="noted_date" name="noted_date" class="form-control">
            </div>
            <div class="col-12">
              <label for="notes" class="form-label">Notas</label>
              <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar alergia</button>
        </div>
      </form>
    </div>
  </div>
</div>
