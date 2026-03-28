// Global overlay helpers used by patients.js and other modules
function showLoadingOverlay(){
  const el = document.getElementById('loading-overlay');
  if (el) el.style.display='flex';
}
function hideLoadingOverlay(){
  const el = document.getElementById('loading-overlay');
  if (el) el.style.display='none';
}

document.addEventListener('DOMContentLoaded', function(){
  // i18n helper and debug
  const t = window.i18n_t || ((k,vars)=> (typeof k === 'string' ? k : ''));
  const DBG = (new URLSearchParams(location.search).get('debug') === '1') || (localStorage.getItem('DEBUG_PRINT') === '1');
  // Patients list and form are now handled in /assets/js/patients.js

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
    const startMinimized = (localStorage.getItem('CHAT_MINIMIZED') ?? '1') === '1';
    const widgetHeight = 420;
    Object.assign(widget.style, {
      position: 'fixed', right: '16px', bottom: startMinimized ? `-${widgetHeight}px` : '16px', zIndex: 1050, boxShadow: '0 6px 18px rgba(0,0,0,0.15)'
    });
    document.body.appendChild(widget);

    const openChatBtn = document.createElement('button');
    openChatBtn.id = 'privateChatOpenBtn';
    openChatBtn.className = 'btn btn-primary btn-sm';
    openChatBtn.title = t('private_chat') || 'Chat';
    openChatBtn.style.position = 'fixed';
    openChatBtn.style.right = '16px';
    openChatBtn.style.bottom = '16px';
    openChatBtn.style.zIndex = 1050;
    openChatBtn.style.width = '44px';
    openChatBtn.style.height = '44px';
    openChatBtn.style.borderRadius = '50%';
    openChatBtn.style.padding = '0';
    openChatBtn.style.display = startMinimized ? '' : 'none';
    openChatBtn.innerHTML = `<i class="fa-solid fa-comments"></i>`;

    const openChatBadge = document.createElement('span');
    openChatBadge.className = 'badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle d-none';
    openChatBtn.appendChild(openChatBadge);

    openChatBtn.addEventListener('click', () => {
      widget.style.bottom = '16px';
      openChatBtn.style.display = 'none';
      setUnread(0);
      localStorage.setItem('CHAT_MINIMIZED', '0');
    });

    document.body.appendChild(openChatBtn);

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

      const showBadge = unreadCount > 0;
      if (showBadge){
        unreadBadge.textContent = String(unreadCount);
        unreadBadge.classList.remove('d-none');
        unreadBadge.classList.add('blink');
      } else {
        unreadBadge.textContent = '0';
        unreadBadge.classList.add('d-none');
        unreadBadge.classList.remove('blink');
      }

      // Update open-chat button badge when minimized
      if (typeof openChatBadge !== 'undefined'){
        if (showBadge && isMinimized()){
          openChatBadge.textContent = String(unreadCount);
          openChatBadge.classList.remove('d-none');
        } else {
          openChatBadge.textContent = '';
          openChatBadge.classList.add('d-none');
        }
      }
    }

    function isMinimized(){ return widget.style.bottom === `-${widgetHeight}px`; }

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
        const hasIncoming = since > 0 && incomingMessages.length > 0;
        if (hasIncoming){
          setUnread(unreadCount + incomingMessages.length);
          if (soundEnabled) {
            // Play a ding for each new message in inbox
            incomingMessages.forEach(()=>playBeep());
          }
          // Only show visual notification when minimized/hidden for non-disruption
          if (isMinimized() || document.hidden) {
            showIncomingNotification(incomingMessages);
          }
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
        openChatBtn.style.display = 'none';
        setUnread(0);
        localStorage.setItem('CHAT_MINIMIZED', '0');
      } else {
      widget.style.bottom = `-${widgetHeight}px`;
        openChatBtn.style.display = '';
        localStorage.setItem('CHAT_MINIMIZED', '1');
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
}); // End of DOMContentLoaded
 