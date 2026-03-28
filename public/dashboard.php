<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-3">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2><i class="fa-solid fa-chart-line me-2"></i><span data-i18n="dashboard">Dashboard</span></h2>
    <small class="text-muted" data-i18n="dashboard_subtitle">Hospital statistics overview</small>
  </div>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card p-3">
        <h5 class="card-title" data-i18n="total_patients">Total Patients</h5>
        <div class="display-6" id="statPatients">...</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5 class="card-title" data-i18n="monthly_encounters">Monthly Encounters</h5>
        <div class="chart-container" style="height:180px;">
          <canvas id="chartEncounters"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h5 class="card-title" data-i18n="top_diagnoses">Top Diagnoses</h5>
        <div class="chart-container" style="height:180px;">
          <canvas id="chartDiagnoses"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mt-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="card-title" data-i18n="patients_by_month">Patients by Month</h5>
        <div class="chart-container" style="height:200px;">
          <canvas id="chartPatients"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="card-title" data-i18n="encounters_by_doctor">Encounters by Doctor</h5>
        <div class="chart-container" style="height:200px;">
          <canvas id="chartEncountersDoctor"></canvas>
        </div>
      </div>
    </div>
  </div> 
  <div id="dashboardError" class="alert alert-danger d-none mt-4" role="alert">
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const t = window.i18n_t || (k=>k);
  const errorBox = document.getElementById('dashboardError');

  function setError(msg){
    if (!errorBox) return;
    if (!msg){
      errorBox.classList.add('d-none');
      errorBox.textContent = '';
      return;
    }
    errorBox.classList.remove('d-none');
    errorBox.textContent = msg;
  }

  function toLabels(data){
    return data.map(r=>r.ym);
  }

  function toCounts(data){
    return data.map(r=>Number(r.cnt));
  }

  async function loadStats(){
    try {
      const res = await fetch('/api/stats_overview.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){
        setError(json.error || t('error') || 'Error');
        return;
      }

      document.getElementById('statPatients').textContent = json.totals?.patients ?? '0';

      const patientsCtx = document.getElementById('chartPatients');
      const encountersCtx = document.getElementById('chartEncounters');
      const diagnosesCtx = document.getElementById('chartDiagnoses');
      const doctorCtx = document.getElementById('chartEncountersDoctor');

      const patientsByMonth = json.patients_by_month || [];
      const encountersByMonth = json.encounters_by_month || [];
      const topDiagnoses = json.top_diagnoses || [];

      new Chart(patientsCtx, {
        type: 'line',
        data: {
          labels: toLabels(patientsByMonth),
          datasets: [{
            label: t('patients'),
            data: toCounts(patientsByMonth),
            borderColor: 'rgba(13,110,253,0.8)',
            backgroundColor: 'rgba(13,110,253,0.2)',
            fill: true,
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      new Chart(encountersCtx, {
        type: 'bar',
        data: {
          labels: toLabels(encountersByMonth),
          datasets: [{
            label: t('encounters'),
            data: toCounts(encountersByMonth),
            backgroundColor: 'rgba(25,135,84,0.7)'
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      new Chart(doctorCtx, {
        type: 'bar',
        data: {
          labels: (json.encounters_by_doctor || []).map(r => r.doctor || t('unknown')),
          datasets: [{
            label: t('encounters'),
            data: (json.encounters_by_doctor || []).map(r => Number(r.cnt)),
            backgroundColor: 'rgba(13,110,253,0.7)'
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      new Chart(diagnosesCtx, {
        type: 'doughnut',
        data: {
          labels: topDiagnoses.map(r=>r.type || 'Unknown'),
          datasets: [{
            data: topDiagnoses.map(r=>Number(r.cnt)),
            backgroundColor: [
              '#0d6efd','#6f42c1','#198754','#fd7e14','#dc3545','#0dcaf0','#6610f2','#20c997','#d63384','#ffc107'
            ]
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

    } catch (err) {
      setError(t('error') || 'Error');
    }
  }

  loadStats();
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
