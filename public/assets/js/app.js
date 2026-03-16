document.addEventListener('DOMContentLoaded', function(){
  // i18n helper and common refs
  const t = window.i18n_t || ((k,vars)=> (typeof k === 'string' ? k : ''));
  const DBG = (new URLSearchParams(location.search).get('debug') === '1') || (localStorage.getItem('DEBUG_PRINT') === '1');
  const tableBody = document.querySelector('#patientsTable tbody');
  const modal = document.getElementById('patientModal') ? new bootstrap.Modal(document.getElementById('patientModal')) : null;
  const form = document.getElementById('patientForm');
  const patientError = document.getElementById('patientFormError');

  // Diagnostic insert logic
  const btnAddDiagnostic = document.getElementById('btnAddDiagnostic');
  const diagnosticForm = document.getElementById('diagnosticForm');
  const btnCancelDiagnostic = document.getElementById('btnCancelDiagnostic');
  const diagnosticFormError = document.getElementById('diagnosticFormError');
  let currentPatientId = null;
  const diagnosticsModal = document.getElementById('diagnosticsModal') ? new bootstrap.Modal(document.getElementById('diagnosticsModal')) : null;
  const diagnosticsContent = document.getElementById('diagnosticsContent');

  if (btnAddDiagnostic && diagnosticForm && btnCancelDiagnostic) {
    btnAddDiagnostic.addEventListener('click', function() {
      diagnosticForm.style.display = '';
      btnAddDiagnostic.style.display = 'none';
      diagnosticFormError.classList.add('d-none');
    });
    btnCancelDiagnostic.addEventListener('click', function() {
      diagnosticForm.style.display = 'none';
      btnAddDiagnostic.style.display = '';
      diagnosticFormError.classList.add('d-none');
      diagnosticForm.reset();
    });
    diagnosticForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const type = document.getElementById('diag_type').value.trim();
      const date = document.getElementById('diag_date').value;
      const description = document.getElementById('diag_description').value.trim();
      const patient_id = document.getElementById('diag_patient_id').value;
      if (!type || !date || !patient_id) {
        diagnosticFormError.textContent = t('error') || 'Error';
        diagnosticFormError.classList.remove('d-none');
        return;
      }
      fetch('/api/diagnostics_create.php', {
        method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, date, description, patient_id })
      }).then(r=>r.json()).then(data=>{
        if (data.success) {
          diagnosticForm.style.display = 'none';
          btnAddDiagnostic.style.display = '';
          diagnosticFormError.classList.add('d-none');
          diagnosticForm.reset();
          // Reload diagnostics list
          if (currentPatientId) loadDiagnostics(currentPatientId);
        } else {
          diagnosticFormError.textContent = data.error || (t('error') || 'Error');
          diagnosticFormError.classList.remove('d-none');
        }
      });
    });
  }

  function loadDiagnostics(patientId) {
    document.getElementById('diag_patient_id').value = patientId;
    diagnosticsContent.innerHTML = '<div class="text-center">'+(t('loading')||'Loading...')+'</div>';
    fetch(`/api/diagnostics_list.php?patient_id=${patientId}`, { credentials: 'same-origin' }).then(r=>r.json()).then(data=>{
      if (!data.success || !Array.isArray(data.diagnostics) || data.diagnostics.length === 0) {
        diagnosticsContent.innerHTML = `<div class="alert alert-info">${t('diagnostics_table_empty')||'No diagnostics found'}</div>`;
        return;
      }
      let html = `<table class="table table-bordered table-sm"><thead><tr><th>${t('diagnostics_type')||'Type'}</th><th>${t('diagnostics_description')||'Description'}</th><th>${t('diagnostics_date')||'Date'}</th><th>${t('diagnostics_created_by')||'Created by'}</th></tr></thead><tbody>`;
      data.diagnostics.forEach(d => {
        html += `<tr><td>${escapeHtml(d.type)}</td><td>${escapeHtml(d.description||'')}</td><td>${d.date||''}</td><td>${escapeHtml(d.created_by_name||'')}</td></tr>`;
      });
      html += '</tbody></table>';
      diagnosticsContent.innerHTML = html;
    }).catch(()=>{
      diagnosticsContent.innerHTML = `<div class="alert alert-info">${t('diagnostics_table_empty')||'No diagnostics found'}</div>`;
    });
  }
function loadPatients() {
  showLoadingOverlay();
  if (!tableBody) {
    hideLoadingOverlay();
    return;
  }

  fetch('/api/patients_list.php', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      tableBody.innerHTML = '';

      const rows = Array.isArray(data.data) ? data.data : [];
      if (!data.success || rows.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">${t('no_data') || 'No data'}</td></tr>`;
        return;
      }

      rows.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${p.id}</td>
          <td>${escapeHtml(p.first_name + ' ' + p.last_name)}</td>
          <td>${escapeHtml(p.cedula||'')}</td>
          <td>${p.dob||''}</td>
          <td>${escapeHtml(p.email||'')}</td>
          <td>${escapeHtml(p.phone||'')}</td>
          <td>
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-primary btn-edit" data-id="${p.id}" >
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button class="btn btn-sm btn-danger btn-del" data-id="${p.id}">
                <i class="fa-solid fa-trash"></i>
              </button>
              <button class="btn btn-sm btn-info btn-diag" data-id="${p.id}">
                <i class="fa-solid fa-stethoscope"></i>
              </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      // Enable Bootstrap tooltips for new buttons
      const tooltipTriggerList = [].slice.call(tableBody.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    })
    .catch(() => {
      tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">${t('no_data') || 'No data'}</td></tr>`;
    })
    .finally(() => hideLoadingOverlay());
}

// Event delegation for icon buttons
tableBody && tableBody.addEventListener('click', e => {
  const button = e.target.closest('button');
  if (!button) return;

  const id = button.dataset.id;
  if (button.classList.contains('btn-diag')) {
    currentPatientId = id;
    loadDiagnostics(id);
    diagnosticsModal && diagnosticsModal.show();
    diagnosticForm.style.display = 'none';
    btnAddDiagnostic.style.display = '';
    diagnosticFormError.classList.add('d-none');
    diagnosticForm.reset();
    return;
  }

  if (button.classList.contains('btn-edit')) {
    fetch('/api/patients_list.php', { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        const p = (Array.isArray(data.data) ? data.data : []).find(x => x.id == id);
        if (!p) return;
        if (patientError) { patientError.classList.add('d-none'); patientError.textContent = ''; }
        if (document.getElementById('id')) document.getElementById('id').value = p.id;
        if (document.getElementById('patientId')) document.getElementById('patientId').value = p.id;
        document.getElementById('first_name').value = p.first_name;
        document.getElementById('last_name').value = p.last_name;
        if (document.getElementById('email')) document.getElementById('email').value = p.email || '';
        document.getElementById('cedula').value = p.cedula || '';
        document.getElementById('dob').value = p.dob || '';
        document.getElementById('gender').value = p.gender || 'O';
        document.getElementById('phone').value = p.phone || '';
        document.getElementById('address').value = p.address || '';
        document.getElementById('notes').value = p.notes || '';
        modal.show();
      });
    return;
  }

  if (button.classList.contains('btn-del')) {
    swal({
      title: t('delete_confirm'),
      text: '',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      confirmButtonText: t('confirm_yes'),
      cancelButtonText: t('cancel'),
      closeOnConfirm: true
    }, function(isConfirm){
      if (!isConfirm) return;
      fetch('/api/patients_delete.php', { method:'POST', credentials: 'same-origin', body: new URLSearchParams({id}) })
        .then(r => r.json())
        .then(res => {
          if (res.success) loadPatients();
          else swal({ title: '', text: res.error || t('error'), type: 'error' });
        });
    });
  }
});

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  if (tableBody) loadPatients();

  const btnAdd = document.getElementById('btnAdd');
  if (btnAdd) {
    btnAdd.addEventListener('click', ()=>{
      if (form && typeof form.reset === 'function') form.reset();
      if (document.getElementById('id')) document.getElementById('id').value = '';
      if (document.getElementById('patientId')) document.getElementById('patientId').value = '';
      if (patientError){ patientError.classList.add('d-none'); patientError.textContent = ''; }
      if (!form || !modal) return;
      if (form && typeof form.reset === 'function') form.reset();
      if (document.getElementById('id')) document.getElementById('id').value = '';
      if (document.getElementById('patientId')) document.getElementById('patientId').value = '';
      if (patientError){ patientError.classList.add('d-none'); patientError.textContent = ''; }
      modal.show();
    });
  }

  // Print table button
  const btnPrintTable = document.getElementById('btnPrintTable');
  if (btnPrintTable) btnPrintTable.addEventListener('click', () => {
    if (DBG) console.info('[PRINT] Table: click');
    const table = document.getElementById('patientsTable');
    if (!table) return swal({ title: '', text: t('no_table_to_print'), type: 'info' });

    // Clone the table so we don't alter the on-page one
    const clonedTable = table.cloneNode(true);

    // Find a column to ignore when printing: prefer [data-print-ignore], fallback to text match
    const headerCells = Array.from(clonedTable.querySelectorAll('thead th'));
    let actionIndex = headerCells.findIndex(th => th.hasAttribute('data-print-ignore'));
    if (actionIndex === -1) {
      actionIndex = headerCells.findIndex(th => th.textContent.trim().toLowerCase() === 'actions');
    }

    // Remove that column from every row (header + body)
    if (actionIndex !== -1) {
      clonedTable.querySelectorAll('tr').forEach(row => {
        const cells = row.querySelectorAll('th, td');
        if (cells[actionIndex]) cells[actionIndex].remove();
      });
    }

    // Ensure strong table styling for print
    clonedTable.className = 'table table-sm table-striped table-bordered print-table';
    clonedTable.style.tableLayout = 'auto';
    clonedTable.style.width = '100%';

    // Now build the printable HTML
    const html = `
    <!doctype html>
    <html>
      <head>
        <meta charset="utf-8">
        <title>${t('patients_title')}</title>
        <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/styles.css" rel="stylesheet">
        <style>
          @media print { .no-print { display:none !important; } }
        </style>
      </head>
      <body data-print-window="table">
        <div class="container mt-4 print-container">
          <div class="print-header d-flex align-items-center mb-3">
            <img src="/assets/images/minsa-logo.svg" alt="Logo" height="40" style="display:block"/>
            <div class="ms-2">
              <h2 class="mb-0">${t('patients_title')}</h2>
              <small class="text-muted">${new Date().toLocaleString()}</small>
            </div>
          </div>
          <div class="table-responsive">${clonedTable.outerHTML}</div>
        </div>
        <script>try{ console.info('[PRINT] Table window ready'); }catch(e){}; window.print && window.print();</script>
      </body>
    </html>`;

    // Open and print
    const w = window.open('', '_blank');
    w.document.write(html);
    w.document.close();
    w.focus();
    // printing is triggered in the child window itself
    setTimeout(() => w.close(), 800);
  });

  // REMOVE DUPLICATE: ensure only one handler exists (kept the one above with diagnostics)
  /*
  // Removed duplicate patients click handler (edit/delete)
  */

  form && form.addEventListener('submit', function(e){
    e.preventDefault();
    // client-side cedula format validation
    const cedVal = document.getElementById('cedula') ? document.getElementById('cedula').value.trim() : '';
    const emailVal = document.getElementById('email') ? document.getElementById('email').value.trim() : '';
    if (cedVal && !/^[\p{L}\d\-\s]+$/u.test(cedVal)) {
      if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('cedula_invalid'); }
      return;
    }
    if (emailVal && !/^\S+@\S+\.\S+$/.test(emailVal)){
      if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('email_invalid'); }
      return;
    }

    const data = new FormData(form);
    const id = data.get('id');
    // check uniqueness via API before submitting
    const url = id ? '/api/patients_update.php' : '/api/patients_create.php';

    const submitPatient = () => {
      fetch(url, { method:'POST', credentials: 'same-origin', body: data })
        .then(r=>r.json())
        .then(res=>{
          if (res.success) {
            if (patientError){ patientError.classList.add('d-none'); patientError.textContent = ''; }
            modal.hide();
            loadPatients();
          } else {
            if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = (res.error||t('error')); }
          }
        })
        .catch(()=>{ if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('error'); } });
    };

    if (cedVal){
      fetch('/api/cedula_check.php?cedula=' + encodeURIComponent(cedVal) + (id ? '&id='+encodeURIComponent(id):''), { credentials: 'same-origin' })
        .then(r=>r.json()).then(j=>{
          if (!j.success) {
            if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('error'); }
            return;
          }
          if (!j.available) {
            if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('cedula_in_use'); }
            return;
          }
          submitPatient();
        }).catch(()=>{ if (patientError){ patientError.classList.remove('d-none'); patientError.textContent = t('error'); } });
    } else {
      submitPatient();
    }
  });

  // Print details from modal
  const btnPrintDetails = document.getElementById('btnPrintDetails');
  function buildDetailsHtml(){
    const id = document.getElementById('patientId').value || '';
    const first = escapeHtml(document.getElementById('first_name').value || '');
    const last = escapeHtml(document.getElementById('last_name').value || '');
    const cedulaVal = escapeHtml(document.getElementById('cedula') ? document.getElementById('cedula').value : '');
    const dob = escapeHtml(document.getElementById('dob').value || '');
    const gender = escapeHtml(document.getElementById('gender').value || '');
    const phone = escapeHtml(document.getElementById('phone').value || '');
    const address = escapeHtml(document.getElementById('address').value || '');
    const notes = escapeHtml(document.getElementById('notes').value || '');
    const email = escapeHtml(document.getElementById('email') ? document.getElementById('email').value : '');
    return `<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>${t('patient_details_title')} ${id}</title>
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet">
  </head>
  <body data-print-window="details">
    <div class="container mt-4 print-container">
      <div class="print-header d-flex align-items-center mb-3">
        <img src="/assets/images/minsa-logo.svg" alt="Logo" height="40" style="display:block"/>
        <div class="ms-2">
          <h2 class="mb-0">${t('patient_details_title')}</h2>
          <small class="text-muted">${new Date().toLocaleString()}</small>
        </div>
      </div>
      <table class="table table-sm table-striped table-bordered print-table">
        <tbody>
          <tr><th style="width:220px;">ID</th><td>${id}</td></tr>
          <tr><th>${t('name')}</th><td>${first} ${last}</td></tr>
          <tr><th>${t('cedula')}</th><td>${cedulaVal}</td></tr>
          <tr><th>${t('email')}</th><td>${email}</td></tr>
          <tr><th>${t('dob')}</th><td>${dob}</td></tr>
          <tr><th>${t('gender')}</th><td>${gender}</td></tr>
          <tr><th>${t('phone')}</th><td>${phone}</td></tr>
          <tr><th>${t('address')}</th><td>${address}</td></tr>
          <tr><th>${t('notes')}</th><td>${notes}</td></tr>
        </tbody>
      </table>
    </div>
    <script>try{ console.info('[PRINT] Details window ready'); }catch(e){}; window.print && window.print();</script>
  </body>
</html>`;
  }

  if (btnPrintDetails) btnPrintDetails.addEventListener('click', ()=>{
    if (DBG) console.info('[PRINT] Details: click');
    const html = buildDetailsHtml();
    const w = window.open('','_blank');
    w.document.write(html); w.document.close(); w.focus(); w.print(); setTimeout(()=>w.close(),500);
  });

  // --- Chat: private user-to-user floating widget ---
  (function(){
    if (!window.CURRENT_USER) return; // only for logged-in users
    const me = window.CURRENT_USER;
    // create widget DOM
    const widget = document.createElement('div');
    widget.id = 'privateChatWidget';
    widget.innerHTML = `
      <div class="card" style="width:320px;height:420px;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <strong class="me-2" data-i18n="private_chat">${t('private_chat')}</strong>
            <span id="chatUnread" class="badge rounded-pill bg-danger d-none">0</span>
            <select id="chatRecipient" class="form-select form-select-sm"></select>
          </div>
          <div class="d-flex align-items-center ms-1 mr-0">
            <button id="chatSound" class="btn btn-sm btn-outline-secondary me-1" title="${t('notifications_on')}"><i class="fa-regular fa-bell"></i></button>
            <button id="chatMin" class="btn btn-sm btn-outline-secondary">–</button>
          </div>
        </div>
        <div class="card-body d-flex flex-column" style="height:calc(100% - 88px);">
          <div id="privateChatPane" class="flex-grow-1 mb-2 overflow-auto border rounded p-2" style="background:#fff;">${t('loading')}</div>
          <div class="d-flex">
            <input id="privateChatInput" class="form-control form-control-sm me-2" placeholder="${t('type_message')}" data-i18n="type_message">
            <button id="privateChatSendAll" class="btn btn-outline-primary btn-sm me-1" title="${t('send_to_all')}" aria-label="${t('send_to_all')}"><i class="fa-solid fa-bullhorn"></i></button>
            <button id="privateChatSend" class="btn btn-primary btn-sm">
              <i class="fa-solid fa-paper-plane me-1"></i>
              <span data-i18n="send">${t('send')}</span>
            </button>
          </div>
        </div>
      </div>`;
    Object.assign(widget.style, {
      position: 'fixed', right: '16px', bottom: '16px', zIndex: 1050, boxShadow: '0 6px 18px rgba(0,0,0,0.15)'
    });
    document.body.appendChild(widget);

    const recipientSelect = document.getElementById('chatRecipient');
    const pane = document.getElementById('privateChatPane');
    const input = document.getElementById('privateChatInput');
    const sendAllBtn = document.getElementById('privateChatSendAll');
    const sendBtn = document.getElementById('privateChatSend');
  const minBtn = document.getElementById('chatMin');
  const soundBtn = document.getElementById('chatSound');
  const unreadBadge = document.getElementById('chatUnread');

    // state: track last id per recipient
    const lastMap = {};
  let pollTimer = null;
    let unreadCount = 0;
  let soundEnabled = (localStorage.getItem('CHAT_SOUND') ?? '1') === '1';

    function setUnread(n){
      unreadCount = Math.max(0, n|0);
      if (unreadCount > 0){
        unreadBadge.textContent = String(unreadCount);
        unreadBadge.classList.remove('d-none');
        unreadBadge.classList.add('blink');
      } else {
        unreadBadge.textContent = '0';
        unreadBadge.classList.add('d-none');
        unreadBadge.classList.remove('blink');
      }
    }

    function isMinimized(){ return widget.style.bottom === '-360px'; }

    function setSoundButton(){
      const icon = soundBtn.querySelector('i');
      if (soundEnabled){
        icon.className = 'fa-regular fa-bell';
        soundBtn.title = t('notifications_on');
      } else {
        icon.className = 'fa-regular fa-bell-slash';
        soundBtn.title = t('notifications_off');
      }
    }

    function playBeep(){
      try{
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'sine';
        o.frequency.value = 880; // A5
        o.connect(g);
        g.connect(ctx.destination);
        g.gain.setValueAtTime(0.0001, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.15, ctx.currentTime + 0.02);
        g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.22);
        o.start();
        o.stop(ctx.currentTime + 0.25);
      }catch(e){ /* ignore */ }
    }

    function showIncomingNotification(messages){
      if (!messages || !messages.length) return;
      const count = messages.length;
      const last = messages[messages.length - 1];
      const text = count === 1
        ? `${(last && last.username) ? last.username : t('private_chat')}: ${last && last.message ? last.message : ''}`
        : `${count} ${t('incoming_messages')}`;

      try{
        if ('Notification' in window && Notification.permission === 'granted'){
          new Notification(t('private_chat'), { body: text });
          return;
        }
      }catch(e){ /* ignore */ }

      if (typeof swal === 'function'){
        swal({
          title: t('private_chat'),
          text,
          type: 'info',
          timer: 2500,
          showConfirmButton: false
        });
      }
    }

    function escapeHtmlLocal(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function renderMessageLocal(m, isMe){
      const div = document.createElement('div');
      div.className = 'mb-1';
      const time = new Date(m.created_at || Date.now()).toLocaleTimeString();
      div.innerHTML = `<small class="text-muted">${escapeHtmlLocal(m.username)} @ ${time}</small><div>${escapeHtmlLocal(m.message)}</div>`;
      if (isMe) div.style.textAlign = 'right';
      pane.appendChild(div);
    }

    async function loadUsers(){
      try{
        const res = await fetch('/api/users_list.php', { credentials: 'same-origin' });
        const j = await res.json();
        if (!j.success) return;
        const others = (j.data || []).filter(u => String(u.id) !== String(me.id));
        recipientSelect.innerHTML = `<option value="">${t('select_user')}</option>` + others.map(u=>`<option value="${u.id}">${escapeHtmlLocal(u.username)}${u.fullname? ' — '+escapeHtmlLocal(u.fullname):''}</option>`).join('');
      }catch(e){ console.warn('users load', e); }
    }

    async function loadConversation(){
      const rid = recipientSelect.value;
      if (!rid) { pane.innerHTML = `<div class="text-muted">${t('select_user')}</div>`; return; }
      try{
        const since = lastMap[rid] || 0;
        const url = '/api/chat_list.php?recipient=' + encodeURIComponent(rid) + (since ? '&since='+since : '');
        const res = await fetch(url, { credentials: 'same-origin' });
        const j = await res.json();
        if (!j.success) return;
        if (!since) pane.innerHTML = '';
        const incomingMessages = [];
        j.data.forEach(m=>{
          const isMine = String(m.user_id) === String(me.id);
          renderMessageLocal(m, isMine);
          lastMap[rid] = Math.max(lastMap[rid]||0, m.id);
          if (!isMine) incomingMessages.push(m);
        });
        pane.scrollTop = pane.scrollHeight;
        const shouldNotify = since > 0 && (isMinimized() || document.hidden) && incomingMessages.length > 0;
        if (shouldNotify){
          setUnread(unreadCount + incomingMessages.length);
          if (soundEnabled) playBeep();
          showIncomingNotification(incomingMessages);
        }
      }catch(e){ console.warn('loadConversation', e); }
    }

    async function sendPrivate(){
    const rid = recipientSelect.value;
    if (!rid) { swal({ title: '', text: t('select_recipient'), type: 'warning' }); return; }
      const msg = input.value.trim(); if (!msg) return;
      sendBtn.disabled = true;
      if (sendAllBtn) sendAllBtn.disabled = true;
      try{
        const res = await fetch('/api/chat_send.php', { method: 'POST', credentials: 'same-origin', body: new URLSearchParams({ message: msg, recipient_id: rid }) });
        const j = await res.json();
        if (j.success){
          renderMessageLocal({ id: j.id, username: j.username, message: j.message, created_at: j.created_at, user_id: me.id }, true);
          lastMap[rid] = Math.max(lastMap[rid]||0, j.id);
          input.value = '';
          pane.scrollTop = pane.scrollHeight;
        } else {
          swal({ title: '', text: (j.error || t('error')), type: 'error' });
        }
  }catch(e){ console.error(e); swal({ title: '', text: t('error'), type: 'error' }); }
      sendBtn.disabled = false;
      if (sendAllBtn) sendAllBtn.disabled = false;
    }

    async function sendToAll(){
      const msg = input.value.trim();
      if (!msg) return;
      sendBtn.disabled = true;
      if (sendAllBtn) sendAllBtn.disabled = true;
      try{
        const res = await fetch('/api/chat_send.php', { method: 'POST', credentials: 'same-origin', body: new URLSearchParams({ message: msg }) });
        const j = await res.json();
        if (j.success){
          renderMessageLocal({ id: j.id, username: j.username, message: j.message, created_at: j.created_at, user_id: me.id }, true);
          input.value = '';
          pane.scrollTop = pane.scrollHeight;
          if (typeof swal === 'function') {
            swal({ title: '', text: t('sent_to_all'), type: 'success', timer: 1200, showConfirmButton: false });
          }
        } else {
          swal({ title: '', text: (j.error || t('error')), type: 'error' });
        }
      }catch(e){
        console.error(e);
        swal({ title: '', text: t('error'), type: 'error' });
      }
      sendBtn.disabled = false;
      if (sendAllBtn) sendAllBtn.disabled = false;
    }

    recipientSelect.addEventListener('change', ()=>{
      // reset view for new recipient
      pane.innerHTML = `<div class="text-muted">${t('loading')}</div>`;
      // clear last only for full reload when not present
      if (!lastMap[recipientSelect.value]) lastMap[recipientSelect.value] = 0;
      setUnread(0);
      loadConversation();
    });
    sendBtn.addEventListener('click', sendPrivate);
    if (sendAllBtn) sendAllBtn.addEventListener('click', sendToAll);
    input.addEventListener('keydown', (e)=>{ if (e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); sendPrivate(); } });
    minBtn.addEventListener('click', ()=>{
      if (isMinimized()){
        widget.style.bottom = '16px';
        setUnread(0);
      } else {
        widget.style.bottom = '-360px';
      }
    });

    soundBtn.addEventListener('click', ()=>{
      soundEnabled = !soundEnabled;
      localStorage.setItem('CHAT_SOUND', soundEnabled ? '1' : '0');
      setSoundButton();
    });

    if ('Notification' in window && Notification.permission === 'default'){
      document.addEventListener('click', function requestChatNotificationPermission(){
        Notification.requestPermission().catch(()=>{});
        document.removeEventListener('click', requestChatNotificationPermission);
      }, { once: true });
    }

    // polling
    function startPoll(){
      if (pollTimer) clearInterval(pollTimer);
      pollTimer = setInterval(()=>{ if (recipientSelect.value) loadConversation(); }, 3000);
    }

    loadUsers().then(()=>{ startPoll(); setSoundButton(); if (window.applyI18n) try{ window.applyI18n(widget); }catch(e){} });
  })();



// Loading overlay
function showLoadingOverlay(){document.getElementById('loading-overlay').style.display='flex';}
function hideLoadingOverlay(){document.getElementById('loading-overlay').style.display='none';}



 
 
}
 
 ); // End of DOMContentLoaded

 