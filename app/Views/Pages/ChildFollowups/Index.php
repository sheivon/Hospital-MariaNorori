<div class="container mt-4" id="childFollowupsPage">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0">Seguimiento Integral Niñez y Adolescencia</h2>
      <p class="text-muted mb-0">Registro rápido de signos clínicos, nutrición y entorno familiar.</p>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="mb-0">Historial de seguimiento</h5>
        </div>
        <div>
          <button id="openChildFollowupModal" type="button" class="btn btn-primary">Agregar seguimiento</button>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table id="childFollowupsTable" class="table table-sm table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Paciente</th>
                  <th>Cédula</th>
                  <th>Fecha de visita</th>
                  <th>Notas</th>
                  <th>Notas asociadas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="childFollowupModal" tabindex="-1" aria-labelledby="childFollowupModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="childFollowupModalTitle">Agregar seguimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="childFollowupsError" class="d-none mb-3"></div>
        <form id="childFollowupsForm">
          <input type="hidden" id="followup_id" name="id" value="">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="patient_id" class="form-label">Paciente</label>
                <select id="patient_id" name="patient_id" class="form-select" required></select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="visit_date" class="form-label">Fecha de visita</label>
                <input type="date" id="visit_date" name="visit_date" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Signos principales</label>
            <div class="row g-2">
              <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="respira_rapida" name="respira_rapida" value="1"><label class="form-check-label" for="respira_rapida">Respira rápida</label></div></div>
              <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="dificultad_alimentarse" name="dificultad_alimentarse" value="1"><label class="form-check-label" for="dificultad_alimentarse">Dificultad alimentarse</label></div></div>
              <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="dificultad_respirar" name="dificultad_respirar" value="1"><label class="form-check-label" for="dificultad_respirar">Dificultad respirar</label></div></div>
              <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="convulsiones" name="convulsiones" value="1"><label class="form-check-label" for="convulsiones">Convulsiones</label></div></div>
              <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="fiebre" name="fiebre" value="1"><label class="form-check-label" for="fiebre">Fiebre</label></div></div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Nutrición</label>
            <div class="row g-2">
              <div class="col-md-4"><input type="number" step="1" min="0" id="peso_g" name="peso_g" class="form-control" placeholder="Peso (g)"></div>
              <div class="col-md-4"><input type="number" step="0.1" min="0" id="talla_cm" name="talla_cm" class="form-control" placeholder="Talla (cm)"></div>
              <div class="col-md-4"><div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="malnutricion" name="malnutricion" value="1"><label class="form-check-label" for="malnutricion">Malnutrición</label></div></div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Vacunas / suplementos</label>
            <div class="row g-2">
              <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="vacuna" name="vacuna" value="1"><label class="form-check-label" for="vacuna">Vacuna</label></div></div>
              <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="vitamina_a" name="vitamina_a" value="1"><label class="form-check-label" for="vitamina_a">Vitamina A</label></div></div>
              <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="hierro" name="hierro" value="1"><label class="form-check-label" for="hierro">Hierro</label></div></div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Entorno familiar</label>
            <div class="form-check"><input class="form-check-input" type="checkbox" id="buen_trato" name="buen_trato" value="1"><label class="form-check-label" for="buen_trato">Buen trato</label></div>
            <div class="mt-2">
              <label for="relacion_afectivo" class="form-label">Relación afectiva</label>
              <select id="relacion_afectivo" name="relacion_afectivo" class="form-select">
                <option value="">Seleccione</option>
                <option value="Madre">Madre</option>
                <option value="Padre">Padre</option>
                <option value="Cuidador">Cuidador</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="notas" class="form-label">Notas</label>
            <textarea id="notas" name="notas" class="form-control" rows="4"></textarea>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="childFollowupNotesModal" tabindex="-1" aria-labelledby="childFollowupNotesModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="childFollowupNotesModalTitle">Agregar nota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="childFollowupNotesError" class="d-none mb-3"></div>
        <form id="childFollowupNotesForm">
          <div class="mb-3">
            <label for="childFollowupNoteTipo" class="form-label">Tipo de nota</label>
            <input type="text" id="childFollowupNoteTipo" name="tipo" class="form-control" placeholder="Ej. Observación" />
          </div>
          <div class="mb-3">
            <label for="childFollowupNoteContenido" class="form-label">Contenido</label>
            <textarea id="childFollowupNoteContenido" name="contenido" class="form-control" rows="4" required></textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar nota</button>
          </div>
        </form>

        <div class="mt-4">
          <h6 class="mb-3">Notas anteriores</h6>
          <ul class="list-group" id="childFollowupNotesList"></ul>
        </div>
      </div>
    </div>
  </div>
</div>
