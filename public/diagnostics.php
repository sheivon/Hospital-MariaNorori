<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0" data-i18n="diagnostics_title"><i class="fa-solid fa-stethoscope me-2"></i>Diagnostics</h2>
    <div class="d-flex align-items-center gap-2">
      <label class="me-2" for="diagPatientFilter" data-i18n="patient">Patient</label>
      <select id="diagPatientFilter" class="form-select" style="min-width:260px"></select>
      <button id="btnDiagAdd" class="btn btn-success"><i class="fa fa-plus me-1"></i><span data-i18n="diagnostics_add_btn">Add Diagnostic</span></button>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped" id="diagnosticsTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="diagnostics_type">Type</th>
              <th data-i18n="diagnostics_description">Description</th>
              <th data-i18n="diagnostics_date">Date</th>
              <th data-i18n="diagnostics_created_by">Created by</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Create/Edit Diagnostic Modal -->
<div class="modal fade" id="diagCrudModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="diagCrudForm">
        <div class="modal-header">
          <h5 class="modal-title" data-i18n="diagnostics_add_btn">Add Diagnostic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="diagCrudError" class="alert alert-danger d-none"></div>
          <input type="hidden" id="diagCrudId">
          <div class="mb-2">
            <label class="form-label" data-i18n="patient">Patient</label>
            <select id="diagCrudPatient" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_type">Type</label>
            <input id="diagCrudType" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_date">Date</label>
            <input id="diagCrudDate" type="date" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label" data-i18n="diagnostics_description">Description</label>
            <textarea id="diagCrudDesc" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="diagnostics_cancel_btn">Cancel</button>
          <button class="btn btn-primary" data-i18n="diagnostics_save_btn">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
<script>
(function(){
  const t = window.i18n_t || ((k)=>k);
  const tbl = document.querySelector('#diagnosticsTable tbody');
  const filter = document.getElementById('diagPatientFilter');
  const btnAdd = document.getElementById('btnDiagAdd');
  const modalEl = document.getElementById('diagCrudModal');
  const modal = new bootstrap.Modal(modalEl);
  const f = document.getElementById('diagCrudForm');
  const err = document.getElementById('diagCrudError');
  const fId = document.getElementById('diagCrudId');
  const fPatient = document.getElementById('diagCrudPatient');
  const fType = document.getElementById('diagCrudType');
  const fDate = document.getElementById('diagCrudDate');
  const fDesc = document.getElementById('diagCrudDesc');

  function setErr(msg){ if (!msg){ err.classList.add('d-none'); err.textContent=''; } else { err.textContent=msg; err.classList.remove('d-none'); } }

  function loadPatients(){
    return fetch('/api/patients_list.php').then(r=>r.json()).then(j=>{
      if (!j.success) return [];
      const opts = ['<option value="">'+(t('select_user')||'Select')+'</option>'];
      fPatient.innerHTML = '';
      filter.innerHTML = '<option value="">'+(t('patient')||'Patient')+': '+(t('all')||'All')+'</option>';
      j.data.forEach(p=>{
        const name = `${p.first_name} ${p.last_name}`.trim();
        const o1 = document.createElement('option'); o1.value = p.id; o1.textContent = name; fPatient.appendChild(o1);
        const o2 = document.createElement('option'); o2.value = p.id; o2.textContent = name; filter.appendChild(o2);
      });
      return j.data;
    });
  }

  function loadDiagnostics(){
    const pid = filter.value;
    const url = pid ? `/api/diagnostics_list.php?patient_id=${encodeURIComponent(pid)}` : '/api/diagnostics_list.php';
    tbl.innerHTML = '<tr><td colspan="5">'+(t('loading')||'Loading...')+'</td></tr>';
    fetch(url).then(r=>r.json()).then(j=>{
      if (!j.success){ tbl.innerHTML = '<tr><td colspan="5">'+(j.error||t('error')||'Error')+'</td></tr>'; return; }
      if (!j.diagnostics || j.diagnostics.length===0){ tbl.innerHTML = '<tr><td colspan="5">'+(t('diagnostics_table_empty')||'No diagnostics found')+'</td></tr>'; return; }
      tbl.innerHTML='';
      j.diagnostics.forEach((d,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${i+1}</td><td>${escapeHtml(d.type)}</td><td>${escapeHtml(d.description||'')}</td><td>${d.date||''}</td><td>${escapeHtml(d.created_by_name||'')}</td>`;
        tbl.appendChild(tr);
      });
    });
  }

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  btnAdd.addEventListener('click', ()=>{
    setErr('');
    f.reset();
    fId.value='';
    // preselect patient from filter if chosen
    if (filter.value) fPatient.value = filter.value; else fPatient.selectedIndex = 0;
    modal.show();
  });

  filter.addEventListener('change', loadDiagnostics);

  f.addEventListener('submit', (e)=>{
    e.preventDefault(); setErr('');
    const payload = {
      id: fId.value ? Number(fId.value) : undefined,
      patient_id: Number(fPatient.value || 0),
      type: fType.value.trim(),
      date: fDate.value,
      description: fDesc.value.trim()
    };
    if (!payload.patient_id || !payload.type || !payload.date){ setErr(t('error')||'Error'); return; }
    const isUpdate = !!payload.id;
    const url = isUpdate ? '/api/diagnostics_update.php' : '/api/diagnostics_create.php';
    fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
      .then(r=>r.json()).then(j=>{
        if (!j.success){ setErr(j.error||t('error')||'Error'); return; }
        modal.hide();
        loadDiagnostics();
      }).catch(()=> setErr(t('error')||'Error'));
  });

  // init
  loadPatients().then(()=> loadDiagnostics());
})();
</script>
<script type="module">
    import DataTable from 'datatables.net-dt';
    $(document).ready(function() {
        $('#diagnosticsTable').DataTable();
    });
</script>
