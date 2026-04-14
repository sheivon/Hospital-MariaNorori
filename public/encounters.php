<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
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
document.addEventListener('DOMContentLoaded', function(){
  const t = window.i18n_t || (k=>k);

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  if ($.fn.dataTable.isDataTable('#encountersTable')) {
    $('#encountersTable').DataTable().destroy();
    $('#encountersTable tbody').off('click');
  }

  const table = $('#encountersTable').DataTable({
    buttons: [
      { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
      { extend: 'print', exportOptions: { columns: ':not(:last-child)' } }
    ],
    ajax: {
      url: '/api/encounters_list.php',
      dataSrc: function(json){
        if (!json || !json.success || !Array.isArray(json.data)){
          swal({ title: '', text: t('error') || 'Error loading encounters', icon: 'error' });
          return [];
        }
        return json.data;
      }
    },
    columns: [
      { data: null, render: (data, type, row, meta) => meta.row + 1 },
      { data: null, render: row => escapeHtml(`${row.patient_first_name || ''} ${row.patient_last_name || ''}`.trim()) },
      { data: 'encounter_date', render: d => escapeHtml(d || '') },
      { data: 'encounter_type', render: d => escapeHtml(d || '') },
      { data: 'triage_level', render: d => escapeHtml(d || '') },
      { data: 'status', render: d => escapeHtml(d || '') },
      { data: 'attending_name', render: d => escapeHtml(d || '') },
      { data: 'id', orderable: false, searchable: false, className: 'text-center', render: id => `
          <div class="btn-group" role="group">
            <button class="btn btn-sm btn-primary btn-edit" data-id="${id}"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="btn btn-sm btn-danger btn-del" data-id="${id}"><i class="fa-solid fa-trash"></i></button>
          </div>` }
    ],
    responsive: true,
    lengthMenu: [10, 25, 50, 100]
  });

  $('#encountersTable tbody').on('click', 'button.btn-edit', function(){
    const id = $(this).data('id');
    if (!id) return;
    window.location.href = '/encounter.php?id=' + encodeURIComponent(id);
  });

  $('#encountersTable tbody').on('click', 'button.btn-del', function(){
    const id = $(this).data('id');
    if (!id) return;
    swal({ title: t('delete_confirm') || 'Delete?', icon: 'warning', buttons: [t('cancel')||'Cancel', t('confirm_yes')||'Yes'], dangerMode: true })
      .then(async confirmed => {
        if (!confirmed) return;
        try {
          const res = await fetch('/api/encounters_delete.php', { method: 'POST', credentials: 'same-origin', body: new URLSearchParams({ id }) });
          const json = await res.json();
          if (json.success) {
            table.ajax.reload(null, false);
          } else {
            swal({ title: '', text: json.error || t('error') || 'Error', icon: 'error' });
          }
        } catch (err) {
          swal({ title: '', text: err.message || t('error') || 'Error', icon: 'error' });
        }
      });
  });

  $('#btnPrintEncounters').on('click', function(){
    window.open('/print.php?resource=encounters', '_blank');
  });
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
