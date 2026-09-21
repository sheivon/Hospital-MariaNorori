<div class="mt-4 print-container">
  <div class="print-header d-flex align-items-center mb-3">
    <img src="/assets/images/Logo-01.png" alt="Logo" style="max-height: 70px; margin-right: 1rem;" />
    <div>
      <h1 id="printPageTitle" data-i18n="print_title">Patient Complete Record</h1>
      <p id="printPageSubtitle" class="text-muted" data-i18n="print_loading">Loading patient data...</p>
    </div>
  </div>

  <div class="card mb-3" id="printStatus">
    <div class="card-body">
      <p id="printStatusText" data-i18n="print_loading_status">Loading...</p>
    </div>
  </div>

  <div id="patientRecordContent" style="display:none;">

    <!-- Patient Demographics -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-user me-1"></i><span data-i18n="print_patient_information">Patient Information</span></h5>
        <dl class="row mb-0">
          <dt class="col-sm-3"><span data-i18n="print_full_name">Full Name</span></dt>
          <dd class="col-sm-9" id="prFullName"></dd>

          <dt class="col-sm-3"><span data-i18n="cedula">Cedula</span></dt>
          <dd class="col-sm-9" id="prCedula"></dd>

          <dt class="col-sm-3"><span data-i18n="print_record_no">Record No.</span></dt>
          <dd class="col-sm-9" id="prExpediente"></dd>

          <dt class="col-sm-3"><span data-i18n="print_date_of_birth">Date of Birth</span></dt>
          <dd class="col-sm-9" id="prDob"></dd>

          <dt class="col-sm-3"><span data-i18n="gender">Gender</span></dt>
          <dd class="col-sm-9" id="prGender"></dd>

          <dt class="col-sm-3"><span data-i18n="phone">Phone</span></dt>
          <dd class="col-sm-9" id="prPhone"></dd>

          <dt class="col-sm-3"><span data-i18n="email">Email</span></dt>
          <dd class="col-sm-9" id="prEmail"></dd>

          <dt class="col-sm-3"><span data-i18n="address">Address</span></dt>
          <dd class="col-sm-9" id="prAddress"></dd>

          <dt class="col-sm-3"><span data-i18n="marital_status">Marital Status</span></dt>
          <dd class="col-sm-9" id="prMarital"></dd>

          <dt class="col-sm-3"><span data-i18n="procedencia">Procedencia</span></dt>
          <dd class="col-sm-9" id="prProcedencia"></dd>

          <dt class="col-sm-3"><span data-i18n="education_level">Education Level</span></dt>
          <dd class="col-sm-9" id="prEducation"></dd>

          <dt class="col-sm-3"><span data-i18n="employer">Employer</span></dt>
          <dd class="col-sm-9" id="prEmployer"></dd>
        </dl>
      </div>
    </div>

    <!-- Insurance & Family -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-shield-halved me-1"></i><span data-i18n="print_insurance_family">Insurance &amp; Family</span></h5>
        <dl class="row mb-0">
          <dt class="col-sm-3"><span data-i18n="insurance_provider">Insurance Provider</span></dt>
          <dd class="col-sm-9" id="prInsurance"></dd>

          <dt class="col-sm-3"><span data-i18n="print_policy_no">Policy No.</span></dt>
          <dd class="col-sm-9" id="prPolicy"></dd>

          <dt class="col-sm-3"><span data-i18n="father_name">Father Name</span></dt>
          <dd class="col-sm-9" id="prFather"></dd>

          <dt class="col-sm-3"><span data-i18n="mother_name">Mother Name</span></dt>
          <dd class="col-sm-9" id="prMother"></dd>
        </dl>
      </div>
    </div>

    <!-- Notes -->
    <div class="card mb-3" id="prNotesCard" style="display:none;">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-sticky-note me-1"></i><span data-i18n="notes">Notes</span></h5>
        <p id="prNotes" class="mb-0"></p>
      </div>
    </div>

    <!-- Allergies -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-triangle-exclamation me-1"></i><span data-i18n="allergies">Allergies</span> <span id="prAllergiesCount" class="badge bg-secondary"></span></h5>
        <div id="prAllergiesSection">
          <p class="text-muted" data-i18n="print_no_allergies">No allergies recorded.</p>
        </div>
      </div>
    </div>

    <!-- Encounters -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-stethoscope me-1"></i><span data-i18n="encounters">Encounters</span> <span id="prEncountersCount" class="badge bg-secondary"></span></h5>
        <div id="prEncountersSection">
          <p class="text-muted" data-i18n="print_no_encounters">No encounters recorded.</p>
        </div>
      </div>
    </div>

    <!-- Diagnostics -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-microscope me-1"></i><span data-i18n="diagnostics_title">Diagnostics</span> <span id="prDiagnosticsCount" class="badge bg-secondary"></span></h5>
        <div id="prDiagnosticsSection">
          <p class="text-muted" data-i18n="print_no_diagnostics">No diagnostics recorded.</p>
        </div>
      </div>
    </div>

    <!-- Appointments -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-calendar-check me-1"></i><span data-i18n="appointments">Appointments</span> <span id="prAppointmentsCount" class="badge bg-secondary"></span></h5>
        <div id="prAppointmentsSection">
          <p class="text-muted" data-i18n="print_no_appointments">No appointments recorded.</p>
        </div>
      </div>
    </div>

    <!-- Exam Requests -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-x-ray me-1"></i><span data-i18n="print_exams_title">Exams / Lab Requests</span> <span id="prExamsCount" class="badge bg-secondary"></span></h5>
        <div id="prExamsSection">
          <p class="text-muted" data-i18n="print_no_exams">No exam requests recorded.</p>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function(){
  const params = new URLSearchParams(window.location.search);
  const patientId = params.get('patient_id');
  const status = document.getElementById('printStatus');
  const statusText = document.getElementById('printStatusText');
  const content = document.getElementById('patientRecordContent');

  const showError = (message) => {
    status.style.display = 'block';
    statusText.textContent = message;
    content.style.display = 'none';
  };

  const esc = (s) => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  const val = (v) => esc(v) || '<span class="text-muted">-</span>';

  const tr = (key, fallback) => {
    let v = '';
    if (window.i18n_t) { v = window.i18n_t(key); }
    return (v && v !== key) ? v : fallback;
  };

  const fill = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = value;
  };

  const genderMap = {
    M: tr('gender_male', 'Masculino'),
    F: tr('gender_female', 'Femenino'),
    O: tr('gender_other', 'Otro')
  };
  const statusBadge = (s) => {
    const map = { open: 'success', closed: 'secondary', active: 'primary', resolved: 'info', pending: 'warning', cancelled: 'danger' };
    const cls = map[String(s).toLowerCase()] || 'secondary';
    return `<span class="badge bg-${cls}">${esc(s)}</span>`;
  };
  const severityBadge = (s) => {
    const map = { mild: 'info', moderate: 'warning', severe: 'danger', critical: 'danger' };
    const cls = map[String(s).toLowerCase()] || 'secondary';
    return `<span class="badge bg-${cls}">${esc(s)}</span>`;
  };

  const renderTable = (columns, rows) => {
    if (!rows || !rows.length) return '';
    let html = '<div class="table-responsive"><table class="table table-sm table-striped table-bordered print-table mb-0">';
    html += '<thead><tr>' + columns.map(c => `<th>${esc(c.label)}</th>`).join('') + '</tr></thead>';
    html += '<tbody>' + rows.map(r => {
      return '<tr>' + columns.map(c => `<td>${val(r[c.field])}</td>`).join('') + '</tr>';
    }).join('') + '</tbody></table></div>';
    return html;
  };

  if (!patientId) {
    showError(tr('print_no_patient_selected', 'No se seleccionó paciente. Seleccione un paciente desde la página de Informes.'));
    return;
  }

  const dateFrom = params.get('date_from') || '';
  const dateTo = params.get('date_to') || '';

  const query = new URLSearchParams({ resource: 'patient_full', patient_id: patientId });
  if (dateFrom) query.set('date_from', dateFrom);
  if (dateTo) query.set('date_to', dateTo);

  fetch('/api/print_data.php?' + query.toString(), { credentials: 'same-origin' })
    .then(r => r.json())
    .then(json => {
      if (!json.success) {
        showError(json.error || tr('print_error_loading', 'Error al cargar los datos del paciente'));
        return;
      }

      const data = json.data || {};
      const p = data.patient || {};

      // Demographics
      fill('prFullName', val(`${p.first_name || ''} ${p.last_name || ''}`.trim()));
      fill('prCedula', val(p.cedula));
      fill('prExpediente', val(p.expediente_no));
      fill('prDob', val(p.dob));
      fill('prGender', val(genderMap[p.gender] || p.gender));
      fill('prPhone', val(p.phone));
      fill('prEmail', val(p.email));
      fill('prAddress', val(p.address));
      fill('prMarital', val(p.marital_status));
      fill('prProcedencia', val(p.procedencia));
      fill('prEducation', val(p.education_level));
      fill('prEmployer', val(p.employer));

      // Insurance & Family
      fill('prInsurance', val(p.insurance_provider));
      fill('prPolicy', val(p.insurance_policy_no));
      fill('prFather', val(p.father_name));
      fill('prMother', val(p.mother_name));

      // Notes
      if (p.notes) {
        document.getElementById('prNotesCard').style.display = 'block';
        fill('prNotes', esc(p.notes));
      }

      // Allergies
      const allergies = data.allergies || [];
      document.getElementById('prAllergiesCount').textContent = allergies.length;
      if (allergies.length > 0) {
        const cols = [
          { label: tr('allergen', 'Alérgeno'), field: 'allergen' },
          { label: tr('reaction', 'Reacción'), field: 'reaction' },
          { label: tr('severity', 'Severidad'), field: 'severity' },
          { label: tr('table_status', 'Estado'), field: 'status' },
          { label: tr('noted_date', 'Fecha de registro'), field: 'noted_date' },
          { label: tr('notes', 'Notas'), field: 'notes' },
        ];
        fill('prAllergiesSection', renderTable(cols, allergies));
      }

      // Encounters
      const encounters = data.encounters || [];
      document.getElementById('prEncountersCount').textContent = encounters.length;
      if (encounters.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: tr('encounters_date', 'Fecha'), field: 'encounter_date' },
          { label: tr('encounters_type', 'Tipo'), field: 'encounter_type' },
          { label: tr('encounters_triage', 'Triaje'), field: 'triage_level' },
          { label: tr('table_status', 'Estado'), field: 'status' },
          { label: tr('encounters_doctor', 'Médico'), field: 'attending_name' },
          { label: tr('encounters_reason', 'Motivo'), field: 'reason_for_visit' },
          { label: tr('notes', 'Notas'), field: 'notes' },
        ];
        fill('prEncountersSection', renderTable(cols, encounters));
      }

      // Diagnostics
      const diagnostics = data.diagnostics || [];
      document.getElementById('prDiagnosticsCount').textContent = diagnostics.length;
      if (diagnostics.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: tr('diagnostics_type', 'Tipo'), field: 'type' },
          { label: tr('diagnostics_icd10', 'Código ICD-10'), field: 'icd10_code' },
          { label: tr('diagnostics_description', 'Descripción'), field: 'description' },
          { label: tr('table_status', 'Estado'), field: 'status' },
          { label: tr('severity', 'Severidad'), field: 'severity' },
          { label: tr('diagnostics_date', 'Fecha'), field: 'date' },
          { label: tr('print_created_by', 'Creado por'), field: 'created_by_name' },
        ];
        fill('prDiagnosticsSection', renderTable(cols, diagnostics));
      }

      // Appointments
      const appointments = data.appointments || [];
      document.getElementById('prAppointmentsCount').textContent = appointments.length;
      if (appointments.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: tr('appointment_table_datetime', 'Fecha y hora'), field: 'appointment_at' },
          { label: tr('provider', 'Proveedor'), field: 'provider_name' },
          { label: tr('appointments_reason', 'Motivo'), field: 'reason' },
          { label: tr('table_status', 'Estado'), field: 'status' },
          { label: tr('notes', 'Notas'), field: 'notes' },
        ];
        fill('prAppointmentsSection', renderTable(cols, appointments));
      }

      // Exams
      const exams = data.exams || [];
      document.getElementById('prExamsCount').textContent = exams.length;
      if (exams.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: tr('exam_type', 'Tipo de examen'), field: 'exam_type_name' },
          { label: tr('exam_request_date', 'Fecha de solicitud'), field: 'request_date' },
          { label: tr('exam_clinical_data', 'Datos clínicos'), field: 'clinical_data' },
          { label: tr('exam_findings', 'Hallazgos'), field: 'findings' },
          { label: tr('exam_conclusions', 'Conclusiones'), field: 'conclusions' },
          { label: tr('table_status', 'Estado'), field: 'status' },
        ];
        fill('prExamsSection', renderTable(cols, exams));
      }

      // Update subtitle
      const subtitleEl = document.getElementById('printPageSubtitle');
      if (subtitleEl) {
        let subtitle = tr('print_complete_record', 'Registro médico completo de {name}').replace('{name}', `${p.first_name || ''} ${p.last_name || ''}`.trim());
        if (dateFrom || dateTo) {
          const from = dateFrom || '...';
          const to = dateTo || '...';
          subtitle += ` (${from} to ${to})`;
        }
        subtitleEl.textContent = subtitle;
      }

      status.style.display = 'none';
      content.style.display = 'block';
      setTimeout(() => window.print(), 300);
    })
    .catch(error => {
      showError(error.message || 'Error loading patient data');
    });
})();
</script>
<script>
(function(){
  const previousLang = localStorage.getItem('lang');
  localStorage.setItem('lang', 'es');

  window.addEventListener('pagehide', () => {
    if (previousLang === null) {
      localStorage.removeItem('lang');
    } else {
      localStorage.setItem('lang', previousLang);
    }
  });
})();
</script>
