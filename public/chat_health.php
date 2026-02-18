<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0" data-i18n="chat_health_title">Chat Health Check</h5>
      <div>
        <button id="btnRunChecks" class="btn btn-primary btn-sm" data-i18n="run_checks">Run checks</button>
      </div>
    </div>
    <div class="card-body">
      <div id="checkAlert" class="alert d-none" role="alert"></div>
      <pre id="checkOutput" class="bg-light p-3 rounded" style="max-height:360px; overflow:auto;"></pre>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const t = window.i18n_t || (k=>k);
  const btn = document.getElementById('btnRunChecks');
  const out = document.getElementById('checkOutput');
  const alertBox = document.getElementById('checkAlert');
  function showAlert(kind, msg){
    alertBox.className = 'alert alert-' + kind;
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
  }
  btn.addEventListener('click', async ()=>{
    alertBox.classList.add('d-none');
    out.textContent = t('loading');
    try{
      const res = await fetch('/api/chat_health.php', { method:'POST' });
      const j = await res.json();
      out.textContent = JSON.stringify(j, null, 2);
      if (j.success){
        const okSchema = j.data && j.data.schema && j.data.schema.ok;
        const okSend = j.data && j.data.send && j.data.send.ok && j.data.send.readBack;
        showAlert(okSchema && okSend ? 'success' : 'warning', okSchema && okSend ? t('check_all_ok') : t('check_partial_warn'));
      } else {
        showAlert('danger', j.error || t('error'));
      }
    }catch(e){
      showAlert('danger', t('error'));
    }
  });
});
</script>
<?php include __DIR__ . '/../templates/footer.php';
