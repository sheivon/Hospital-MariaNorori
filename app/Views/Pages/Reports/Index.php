<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0">
        <i class="fa-solid fa-file-lines me-2"></i>
        <span data-i18n="reports_title">Reports</span>
      </h2>
      <p class="text-muted mb-0" data-i18n="reports_description">Generate printable reports for patients, exams, and diagnostics.</p>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <div class="row gy-3 align-items-end">
        <div class="col-md-6">
          <label for="reportPatientFilter" class="form-label mb-1" data-i18n="patient">Patient</label>
          <select id="reportPatientFilter" class="form-select">
            <option value="">All patients</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title" data-i18n="patients">Patients</h5>
          <p class="card-text text-muted" data-i18n="reports_patients_description">Export the full patient registry.</p>
          <button id="btnGeneratePatients" class="btn btn-primary w-100">
            <i class="fa-solid fa-file-lines me-1"></i>
            <span data-i18n="generate_report">Generate</span>
          </button>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title" data-i18n="tests_title">Exams</h5>
          <p class="card-text text-muted" data-i18n="reports_tests_description">Export exam results, optionally filtered by patient.</p>
          <button id="btnGenerateTests" class="btn btn-primary w-100">
            <i class="fa-solid fa-file-lines me-1"></i>
            <span data-i18n="generate_report">Generate</span>
          </button>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <h5 class="card-title" data-i18n="diagnostics_title">Diagnostics</h5>
          <p class="card-text text-muted" data-i18n="reports_diagnostics_description">Export diagnostic records, optionally filtered by patient.</p>
          <button id="btnGenerateDiagnostics" class="btn btn-primary w-100">
            <i class="fa-solid fa-file-lines me-1"></i>
            <span data-i18n="generate_report">Generate</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const patientFilter = document.getElementById('reportPatientFilter');
  const patientsButton = document.getElementById('btnGeneratePatients');
  const testsButton = document.getElementById('btnGenerateTests');
  const diagnosticsButton = document.getElementById('btnGenerateDiagnostics');

  const buildUrl = (resource) => {
    const params = new URLSearchParams();
    params.set('resource', resource);
    const patientId = patientFilter.value;
    if (patientId && resource !== 'patients') {
      params.set('patient_id', patientId);
    }
    return `/print.php?${params.toString()}`;
  };

  const openReport = (resource) => {
    window.open(buildUrl(resource), '_blank');
  };

  patientsButton.addEventListener('click', () => openReport('patients'));
  testsButton.addEventListener('click', () => openReport('tests'));
  diagnosticsButton.addEventListener('click', () => openReport('diagnostics'));

  const loadPatients = async () => {
    try {
      const res = await fetch('/api/patients_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success || !Array.isArray(json.data)) {
        return;
      }
      const rows = json.data.map(patient => {
        const label = `${patient.first_name || ''} ${patient.last_name || ''}`.trim() || patient.cedula || 'Patient';
        return `<option value="${encodeURIComponent(patient.id)}">${label}</option>`;
      }).join('');
      patientFilter.innerHTML = `<option value="">${window.i18n_t ? window.i18n_t('all_patients') : 'All patients'}</option>${rows}`;
    } catch (e) {
      // ignore silently
    }
  };

  loadPatients();
})();
</script>
