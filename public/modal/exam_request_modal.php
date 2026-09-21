<div class="modal fade" id="examRequestModal" tabindex="-1" aria-labelledby="examRequestModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form id="examRequestForm">
        <div class="modal-header">
          <h5 class="modal-title" id="examRequestModalTitle" data-i18n="exam_request_title">Solicitud de Examen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="examAlert" class="alert d-none" role="alert"></div>
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#exam-patient-pane" type="button" role="tab">Paciente</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#exam-request-pane" type="button" role="tab">Solicitud</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#exam-clinical-pane" type="button" role="tab">Datos clínicos</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#exam-result-pane" type="button" role="tab">Radiología y resultado</button></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="exam-patient-pane" role="tabpanel">
              <div class="row g-3 align-items-end">
                <input type="hidden" id="patientId" name="patient_id">
                <div class="col-lg-8"><label class="form-label" for="patientDisplay">Paciente *</label><div class="input-group"><input id="patientDisplay" class="form-control" readonly required placeholder="Seleccione un paciente"><button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#patientsListModal">Buscar paciente</button></div></div>
                <div class="col-lg-4"><label class="form-label" for="examTypeId">Tipo de examen *</label><select id="examTypeId" name="exam_type_id" class="form-select" required><option value="">Cargando...</option></select></div>
                <div class="col-md-4"><label class="form-label" for="requestDate">Fecha de solicitud *</label><input id="requestDate" name="request_date" type="date" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label" for="status">Estado</label><select id="status" name="status" class="form-select"><option value="pending">Pendiente</option><option value="completed">Completado</option><option value="cancelled">Cancelado</option></select></div>
                <div class="col-md-4"><label class="form-label" for="code">Código</label><input id="code" name="code" class="form-control" maxlength="50"></div>
              </div>
            </div>
            <div class="tab-pane fade" id="exam-request-pane" role="tabpanel"><div class="row g-3">
              <div class="col-md-3"><label class="form-label" for="unit">Unidad</label><input id="unit" name="unit" class="form-control" maxlength="120"></div>
              <div class="col-md-2"><label class="form-label" for="insured">Asegurado</label><input id="insured" name="insured" class="form-control" maxlength="10"></div>
              <div class="col-md-3"><label class="form-label" for="clinicBed">Clínica/cama</label><input id="clinicBed" name="clinic_bed" class="form-control" maxlength="100"></div>
              <div class="col-md-4"><label class="form-label" for="service">Servicio</label><input id="service" name="service" class="form-control" maxlength="100"></div>
              <div class="col-md-4"><label class="form-label" for="priorRadiograph">Radiografía previa</label><input id="priorRadiograph" name="prior_radiograph" class="form-control" maxlength="50"></div>
              <div class="col-md-4"><label class="form-label" for="priorRadiographCode">Código radiografía previa</label><input id="priorRadiographCode" name="prior_radiograph_code" class="form-control" maxlength="50"></div>
              <div class="col-md-4"><label class="form-label" for="evolutionTime">Tiempo de evolución</label><input id="evolutionTime" name="evolution_time" class="form-control" maxlength="100"></div>
              <div class="col-12"><label class="form-label" for="examRequested">Examen solicitado</label><textarea id="examRequested" name="exam_requested" class="form-control" rows="3"></textarea></div>
            </div></div>
            <div class="tab-pane fade" id="exam-clinical-pane" role="tabpanel"><div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="clinicalData">Datos clínicos</label><textarea id="clinicalData" name="clinical_data" class="form-control" rows="4"></textarea></div>
              <div class="col-md-6"><label class="form-label" for="presumptiveDiagnosis">Diagnóstico presuntivo</label><textarea id="presumptiveDiagnosis" name="presumptive_diagnosis" class="form-control" rows="4"></textarea></div>
              <div class="col-md-6"><label class="form-label" for="notes">Notas</label><textarea id="notes" name="notes" class="form-control" rows="4"></textarea></div>
              <div class="col-md-6"><label class="form-label" for="observations">Observaciones</label><textarea id="observations" name="observations" class="form-control" rows="4"></textarea></div>
            </div></div>
            <div class="tab-pane fade" id="exam-result-pane" role="tabpanel"><div class="row g-3">
              <div class="col-md-3"><label class="form-label" for="doctorCode">Código médico</label><input id="doctorCode" name="doctor_code" class="form-control" maxlength="100"></div>
              <div class="col-md-3"><label class="form-label" for="technician">Técnico</label><input id="technician" name="technician" class="form-control" maxlength="100"></div>
              <div class="col-md-2"><label class="form-label" for="platesUsed">Placas usadas</label><input id="platesUsed" name="plates_used" class="form-control" maxlength="50"></div>
              <div class="col-md-2"><label class="form-label" for="radiologyDate">Fecha radiología</label><input id="radiologyDate" name="radiology_date" type="date" class="form-control"></div>
              <div class="col-md-2"><label class="form-label" for="radiographCount">Cantidad</label><input id="radiographCount" name="radiograph_count" class="form-control" maxlength="50"></div>
              <div class="col-md-4"><label class="form-label" for="radiographsArchived">Radiografías archivadas</label><input id="radiographsArchived" name="radiographs_archived" class="form-control" maxlength="100"></div>
              <div class="col-md-4"><label class="form-label" for="dictatingDoctorCode">Código médico dictante</label><input id="dictatingDoctorCode" name="dictating_doctor_code" class="form-control" maxlength="100"></div>
              <div class="col-12"><label class="form-label" for="findings">Hallazgos</label><textarea id="findings" name="findings" class="form-control" rows="3"></textarea></div>
              <div class="col-12"><label class="form-label" for="conclusions">Conclusiones</label><textarea id="conclusions" name="conclusions" class="form-control" rows="3"></textarea></div>
              <div class="col-12"><label class="form-label" for="result">Resultado</label><textarea id="result" name="result" class="form-control" rows="4"></textarea></div>
            </div></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button id="submitExamRequest" type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar solicitud</button></div>
      </form>
    </div>
  </div>
</div>