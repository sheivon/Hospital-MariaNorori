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
        <div class="col-md-4">
          <label for="reportPatientFilter" class="form-label mb-1" data-i18n="patient">Patient</label>
          <select id="reportPatientFilter" class="form-select">
            <option value="">All patients</option>
          </select>
        </div>

        <div class="col-md-3">
          <label for="reportDateFrom" class="form-label mb-1" data-i18n="from_date">From date</label>
          <input type="date" class="form-control" id="reportDateFrom">
        </div>

        <div class="col-md-3">
          <label for="reportDateTo" class="form-label mb-1" data-i18n="to_date">To date</label>
          <input type="date" class="form-control" id="reportDateTo">
        </div>

        <div class="col-md-2">
          <button id="btnClearReportFilters" class="btn btn-outline-secondary w-100" data-i18n="clear_filters">Clear filters</button>
        </div>
      </div>
      <small class="text-muted d-block mt-2" data-i18n="reports_date_hint">
        Leave dates empty to include the full system history.
      </small>
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
          <h5 class="card-title" data-i18n="diagnostics_title">Diagnostics</h5>
          <p class="card-text text-muted" data-i18n="reports_diagnostics_description">Export diagnostic records, optionally filtered by patient.</p>
          <button id="btnGenerateDiagnostics" class="btn btn-primary w-100">
            <i class="fa-solid fa-file-lines me-1"></i>
            <span data-i18n="generate_report">Generate</span>
          </button>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card h-100 border-primary">
        <div class="card-body">
          <h5 class="card-title">
            <i class="fa-solid fa-clock-rotate-left me-1"></i>
            <span data-i18n="reports_history_title">Patient History</span>
          </h5>
          <p class="card-text text-muted" data-i18n="reports_history_description">Complete historical encounter timeline for all patients, or only the selected one.</p>
          <button id="btnGenerateHistory" class="btn btn-primary w-100">
            <i class="fa-solid fa-file-lines me-1"></i>
            <span data-i18n="generate_report">Generate</span>
          </button>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="card h-100 border-success">
        <div class="card-body">
          <h5 class="card-title">
            <i class="fa-solid fa-id-card me-1"></i>
            <span>Print Full Record</span>
          </h5>
          <p class="card-text text-muted">Print complete patient record including demographics, encounters, diagnostics, allergies, appointments, and exams.</p>
          <button id="btnPrintFullRecord" class="btn btn-success w-100" disabled>
            <i class="fa-solid fa-print me-1"></i>
            <span>Print Record</span>
          </button>
          <small class="text-muted d-block mt-1">Select a patient first</small>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const patientFilter = document.getElementById('reportPatientFilter');
  const dateFrom = document.getElementById('reportDateFrom');
  const dateTo = document.getElementById('reportDateTo');
  const clearButton = document.getElementById('btnClearReportFilters');
  const patientsButton = document.getElementById('btnGeneratePatients');
  const diagnosticsButton = document.getElementById('btnGenerateDiagnostics');
  const historyButton = document.getElementById('btnGenerateHistory');
  const fullRecordButton = document.getElementById('btnPrintFullRecord');

  const t = window.i18n_t || (key => key);

  const buildUrl = (resource) => {
    const params = new URLSearchParams();
    params.set('resource', resource);
    const patientId = patientFilter.value;
    if (patientId && resource !== 'patients') {
      params.set('patient_id', patientId);
    }
    if (dateFrom.value) params.set('date_from', dateFrom.value);
    if (dateTo.value) params.set('date_to', dateTo.value);
    return `/print.php?${params.toString()}`;
  };

  const openReport = (resource) => {
    window.open(buildUrl(resource), '_blank');
  };

  patientsButton.addEventListener('click', () => openReport('patients'));
  diagnosticsButton.addEventListener('click', () => openReport('diagnostics'));
  historyButton.addEventListener('click', () => openReport('patient_history'));

  fullRecordButton.addEventListener('click', () => {
    const patientId = patientFilter.value;
    if (!patientId) return;
    const params = new URLSearchParams({ patient_id: patientId });
    if (dateFrom.value) params.set('date_from', dateFrom.value);
    if (dateTo.value) params.set('date_to', dateTo.value);
    window.open(`/print_patient_record.php?${params.toString()}`, '_blank');
  });

  patientFilter.addEventListener('change', () => {
    const hasPatient = !!patientFilter.value;
    fullRecordButton.disabled = !hasPatient;
    fullRecordButton.closest('.card').querySelector('small').textContent = hasPatient ? '' : 'Select a patient first';
  });

  clearButton.addEventListener('click', () => {
    patientFilter.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    fullRecordButton.disabled = true;
    fullRecordButton.closest('.card').querySelector('small').textContent = 'Select a patient first';
  });

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
      patientFilter.innerHTML = `<option value="">${t('all_patients')}</option>${rows}`;
    } catch (e) {
      // ignore silently
    }
  };

  loadPatients();
})();
</script>
