<div class="mt-4 print-container">
  <div class="print-header d-flex align-items-center mb-3">
    <img src="/assets/images/Logo-01.png" alt="Logo" style="max-height: 70px; margin-right: 1rem;" />
    <div>
      <h1 id="printPageTitle">Patient Complete Record</h1>
      <p id="printPageSubtitle" class="text-muted">Loading patient data...</p>
    </div>
  </div>

  <div class="card mb-3" id="printStatus">
    <div class="card-body">
      <p id="printStatusText">Loading...</p>
    </div>
  </div>

  <div id="patientRecordContent" style="display:none;">

    <!-- Patient Demographics -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-user me-1"></i> Patient Information</h5>
        <dl class="row mb-0">
          <dt class="col-sm-3">Full Name</dt>
          <dd class="col-sm-9" id="prFullName"></dd>

          <dt class="col-sm-3">Cedula</dt>
          <dd class="col-sm-9" id="prCedula"></dd>

          <dt class="col-sm-3">Expediente No.</dt>
          <dd class="col-sm-9" id="prExpediente"></dd>

          <dt class="col-sm-3">Date of Birth</dt>
          <dd class="col-sm-9" id="prDob"></dd>

          <dt class="col-sm-3">Gender</dt>
          <dd class="col-sm-9" id="prGender"></dd>

          <dt class="col-sm-3">Phone</dt>
          <dd class="col-sm-9" id="prPhone"></dd>

          <dt class="col-sm-3">Email</dt>
          <dd class="col-sm-9" id="prEmail"></dd>

          <dt class="col-sm-3">Address</dt>
          <dd class="col-sm-9" id="prAddress"></dd>

          <dt class="col-sm-3">Marital Status</dt>
          <dd class="col-sm-9" id="prMarital"></dd>

          <dt class="col-sm-3">Procedencia</dt>
          <dd class="col-sm-9" id="prProcedencia"></dd>

          <dt class="col-sm-3">Education Level</dt>
          <dd class="col-sm-9" id="prEducation"></dd>

          <dt class="col-sm-3">Employer</dt>
          <dd class="col-sm-9" id="prEmployer"></dd>
        </dl>
      </div>
    </div>

    <!-- Insurance & Family -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-shield-halved me-1"></i> Insurance & Family</h5>
        <dl class="row mb-0">
          <dt class="col-sm-3">Insurance Provider</dt>
          <dd class="col-sm-9" id="prInsurance"></dd>

          <dt class="col-sm-3">Policy No.</dt>
          <dd class="col-sm-9" id="prPolicy"></dd>

          <dt class="col-sm-3">Father Name</dt>
          <dd class="col-sm-9" id="prFather"></dd>

          <dt class="col-sm-3">Mother Name</dt>
          <dd class="col-sm-9" id="prMother"></dd>
        </dl>
      </div>
    </div>

    <!-- Notes -->
    <div class="card mb-3" id="prNotesCard" style="display:none;">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-sticky-note me-1"></i> Notes</h5>
        <p id="prNotes" class="mb-0"></p>
      </div>
    </div>

    <!-- Allergies -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-triangle-exclamation me-1"></i> Allergies <span id="prAllergiesCount" class="badge bg-secondary"></span></h5>
        <div id="prAllergiesSection">
          <p class="text-muted">No allergies recorded.</p>
        </div>
      </div>
    </div>

    <!-- Encounters -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-stethoscope me-1"></i> Encounters <span id="prEncountersCount" class="badge bg-secondary"></span></h5>
        <div id="prEncountersSection">
          <p class="text-muted">No encounters recorded.</p>
        </div>
      </div>
    </div>

    <!-- Diagnostics -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-microscope me-1"></i> Diagnostics <span id="prDiagnosticsCount" class="badge bg-secondary"></span></h5>
        <div id="prDiagnosticsSection">
          <p class="text-muted">No diagnostics recorded.</p>
        </div>
      </div>
    </div>

    <!-- Appointments -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-calendar-check me-1"></i> Appointments <span id="prAppointmentsCount" class="badge bg-secondary"></span></h5>
        <div id="prAppointmentsSection">
          <p class="text-muted">No appointments recorded.</p>
        </div>
      </div>
    </div>

    <!-- Exam Requests -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3"><i class="fa-solid fa-x-ray me-1"></i> Exams / Lab Requests <span id="prExamsCount" class="badge bg-secondary"></span></h5>
        <div id="prExamsSection">
          <p class="text-muted">No exam requests recorded.</p>
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

  const fill = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.innerHTML = value;
  };

  const genderMap = { M: 'Male', F: 'Female', O: 'Other' };
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
    showError('No patient selected. Please select a patient from the Reports page.');
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
        showError(json.error || 'Error loading patient data');
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
          { label: 'Allergen', field: 'allergen' },
          { label: 'Reaction', field: 'reaction' },
          { label: 'Severity', field: 'severity' },
          { label: 'Status', field: 'status' },
          { label: 'Noted Date', field: 'noted_date' },
          { label: 'Notes', field: 'notes' },
        ];
        fill('prAllergiesSection', renderTable(cols, allergies));
      }

      // Encounters
      const encounters = data.encounters || [];
      document.getElementById('prEncountersCount').textContent = encounters.length;
      if (encounters.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: 'Date', field: 'encounter_date' },
          { label: 'Type', field: 'encounter_type' },
          { label: 'Triage', field: 'triage_level' },
          { label: 'Status', field: 'status' },
          { label: 'Doctor', field: 'attending_name' },
          { label: 'Reason', field: 'reason_for_visit' },
          { label: 'Notes', field: 'notes' },
        ];
        fill('prEncountersSection', renderTable(cols, encounters));
      }

      // Diagnostics
      const diagnostics = data.diagnostics || [];
      document.getElementById('prDiagnosticsCount').textContent = diagnostics.length;
      if (diagnostics.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: 'Type', field: 'type' },
          { label: 'ICD-10', field: 'icd10_code' },
          { label: 'Description', field: 'description' },
          { label: 'Status', field: 'status' },
          { label: 'Severity', field: 'severity' },
          { label: 'Date', field: 'date' },
          { label: 'Doctor', field: 'created_by_name' },
        ];
        fill('prDiagnosticsSection', renderTable(cols, diagnostics));
      }

      // Appointments
      const appointments = data.appointments || [];
      document.getElementById('prAppointmentsCount').textContent = appointments.length;
      if (appointments.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: 'Date & Time', field: 'appointment_at' },
          { label: 'Provider', field: 'provider_name' },
          { label: 'Reason', field: 'reason' },
          { label: 'Status', field: 'status' },
          { label: 'Notes', field: 'notes' },
        ];
        fill('prAppointmentsSection', renderTable(cols, appointments));
      }

      // Exams
      const exams = data.exams || [];
      document.getElementById('prExamsCount').textContent = exams.length;
      if (exams.length > 0) {
        const cols = [
          { label: 'ID', field: 'id' },
          { label: 'Exam Type', field: 'exam_type_name' },
          { label: 'Request Date', field: 'request_date' },
          { label: 'Clinical Data', field: 'clinical_data' },
          { label: 'Findings', field: 'findings' },
          { label: 'Conclusions', field: 'conclusions' },
          { label: 'Status', field: 'status' },
        ];
        fill('prExamsSection', renderTable(cols, exams));
      }

      // Update subtitle
      const subtitleEl = document.getElementById('printPageSubtitle');
      if (subtitleEl) {
        let subtitle = `Complete medical record for ${p.first_name || ''} ${p.last_name || ''}`.trim();
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
