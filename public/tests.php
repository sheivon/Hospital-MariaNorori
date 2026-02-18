<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="tests_title">Patient Tests</h3>
    <div>
      <button id="btnNewTest" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="add_test">Add test</span></button>
    </div>
  </div>
  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-striped table-sm" id="testsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th data-i18n="patient">Patient</th>
              <th data-i18n="test_type">Test type</th>
              <th data-i18n="result">Result</th>
              <th data-i18n="test_date">Test date</th>
              <th data-i18n="created_by">Created by</th>
              <th data-i18n="created_at">Created at</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="testForm">
        <div class="modal-header">
          <h5 class="modal-title" id="testModalTitle" data-i18n="add_test">Add test</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="testFormError" class="alert alert-danger d-none"></div>
          <input type="hidden" name="id" id="testId">
          <div class="mb-3">
            <label class="form-label" data-i18n="patient">Patient ID</label>
            <input class="form-control" name="patient_id" id="test_patient_id" required>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="test_type">Test type</label>
            <input class="form-control" name="test_type" id="test_type" required>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="result">Result</label>
            <textarea class="form-control" name="result" id="result" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="test_date">Test date</label>
            <input type="datetime-local" class="form-control" name="test_date" id="test_date">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="notes">Notes</label>
            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="Close">Close</button>
          <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i><span data-i18n="save">Save</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const t = window.i18n_t || (k=>k);
  const modal = new bootstrap.Modal(document.getElementById('testModal'));
  const form = document.getElementById('testForm');
  const errBox = document.getElementById('testFormError');

  // load DataTables Buttons local assets if available (build step copies them to /assets/vendor/datatables)
  // Buttons JS/CSS are loaded in footer/template normally; ensure buttons exist by configuring dom/buttons
  const testsTable = $('#testsTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'print', exportOptions: { columns: ':not(:last-child)' } }
    ],
    ajax: { url: '/api/tests_list.php', dataSrc: function(json){ return (json && json.success && Array.isArray(json.data)) ? json.data : []; } },
    columns: [
      { data: 'id' },
      { data: null, render: d=> (d.first_name? d.first_name+' '+(d.last_name||'') : d.patient_id) },
      { data: 'test_type' },
      { data: 'result' },
      { data: 'test_date' },
      { data: 'created_by_name' },
      { data: 'created_at' }
    ],
    responsive: true
  });

  document.getElementById('btnNewTest').addEventListener('click', ()=>{ form.reset(); errBox.classList.add('d-none'); modal.show(); });

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    errBox.classList.add('d-none'); errBox.textContent='';
    const data = new FormData(form);
    const payload = {};
    data.forEach((v,k)=>{ if (v !== null && v !== '') payload[k]=v; });
    try{
      const res = await fetch('/api/tests_create.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
      const j = await res.json();
      if (j.success){ modal.hide(); testsTable.ajax.reload(); }
      else { errBox.classList.remove('d-none'); errBox.textContent = j.error || t('error'); }
    }catch(err){ errBox.classList.remove('d-none'); errBox.textContent = t('error'); }
  });

});
</script>

<?php include __DIR__ . '/../templates/footer.php';
