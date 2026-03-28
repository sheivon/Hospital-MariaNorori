<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0"><i class="fa-solid fa-notes-medical me-2"></i><span data-i18n="encounters">Encounters</span></h2>
    <div>
      <button id="btnPrintEncounters" class="btn btn-secondary me-2"><i class="fa-solid fa-print me-1"></i><span data-i18n="print">Print</span></button>
      <a href="/encounter.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="add_encounter">Add Encounter</span></a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" id="encountersTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="patient">Patient</th>
              <th data-i18n="encounters_date">Date</th>
              <th data-i18n="encounters_type">Type</th>
              <th data-i18n="triage_level">Triage</th>
              <th data-i18n="encounters_status">Status</th>
              <th data-i18n="encounters_doctor">Doctor</th>
              <th data-i18n="actions">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const t = window.i18n_t || (k=>k);

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  async function loadEncounters(){
    const tbl = document.querySelector('#encountersTable tbody');
    tbl.innerHTML = `<tr><td colspan="7">${t('loading')||'Loading...'}</td></tr>`;

    try {
      const res = await fetch('/api/encounters_list.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.success){
        tbl.innerHTML = `<tr><td colspan="7">${escapeHtml(json.error || t('error') || 'Error')}</td></tr>`;
        return;
      }

      const rows = Array.isArray(json.data) ? json.data : [];
      if (rows.length === 0){
        tbl.innerHTML = `<tr><td colspan="7">${t('no_data')||'No data'}</td></tr>`;
        return;
      }

      tbl.innerHTML = '';
      rows.forEach((r,i)=>{
        const tr = document.createElement('tr');
        const patient = `${escapeHtml(r.patient_first_name||'')} ${escapeHtml(r.patient_last_name||'')}`.trim();
        tr.innerHTML = `
          <td>${i+1}</td>
          <td>${patient}</td>
          <td>${escapeHtml(r.encounter_date||'')}</td>
          <td>${escapeHtml(r.encounter_type||'')}</td>
          <td>${escapeHtml(r.triage_level||'')}</td>
          <td>${escapeHtml(r.status||'')}</td>
          <td>${escapeHtml(r.attending_name||'')}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-primary btn-edit" data-id="${r.id}"><i class="fa-solid fa-pen-to-square"></i></button>
              <button class="btn btn-sm btn-danger btn-del" data-id="${r.id}"><i class="fa-solid fa-trash"></i></button>
            </div>
          </td>
        `;
        tbl.appendChild(tr);
      });
    } catch (e) {
      tbl.innerHTML = `<tr><td colspan="7">${t('error')||'Error'}</td></tr>`;
    }
  }

  document.querySelector('#encountersTable tbody').addEventListener('click', e=>{
    const btn = e.target.closest('button');
    if (!btn) return;
    const id = btn.dataset.id;
    if (btn.classList.contains('btn-edit')){
      window.location.href = '/encounter.php?id=' + encodeURIComponent(id);
      return;
    }
    if (btn.classList.contains('btn-del')){
      if (!confirm(t('delete_confirm') || 'Delete?')) return;
      fetch('/api/encounters_delete.php', { method:'POST', credentials:'same-origin', body: new URLSearchParams({ id }) })
        .then(r=>r.json()).then(j=>{ if (j.success) loadEncounters(); else alert(j.error||t('error')||'Error'); });
    }
  });

  const btnPrintEncounters = document.getElementById('btnPrintEncounters');
  if (btnPrintEncounters) {
    btnPrintEncounters.addEventListener('click', () => {
      const table = document.getElementById('encountersTable');
      if (!table) return alert(t('no_table_to_print') || 'No table to print');
      window.open('/print.php?resource=encounters', '_blank');
    });
  }

  loadEncounters();
})();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
