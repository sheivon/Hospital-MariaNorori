<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0"><i class="fa-solid fa-door-open me-2"></i><span data-i18n="altas_title">Altas</span></h2>
      <p class="text-muted mb-0" data-i18n="altas_description">Encuentros abiertos pendientes de alta.</p>
    </div>
    <button type="button" class="btn btn-outline-secondary" id="btnRefreshAltas"><i class="fa-solid fa-rotate me-1"></i><span data-i18n="data_manager_refresh">Actualizar</span></button>
  </div>

  <div id="altasAlert" class="alert alert-danger d-none" role="alert"></div>

  <div id="altasEmpty" class="alert alert-info d-none" role="alert" data-i18n="altas_empty">No hay encuentros abiertos.</div>

  <div class="row g-3" id="altasCards"></div>
</div>

<script>
(function(){
  const t = (...args) => (window.i18n_t || ((k) => k))(...args);
  const cardsRow = document.getElementById('altasCards');
  const alertBox = document.getElementById('altasAlert');
  const emptyBox = document.getElementById('altasEmpty');
  const btnRefresh = document.getElementById('btnRefreshAltas');

  function escapeHtml(s) {
    return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function waitForI18n(timeoutMs = 2000) {
    return new Promise((resolve) => {
      if (window.i18n) return resolve();
      const started = Date.now();
      const timer = setInterval(() => {
        if (window.i18n || Date.now() - started > timeoutMs) {
          clearInterval(timer);
          resolve();
        }
      }, 50);
    });
  }

  function typeLabel(type) {
    const keys = { outpatient: 'encounter_type_outpatient', inpatient: 'encounter_type_inpatient', emergency: 'encounter_type_emergency' };
    const key = keys[type];
    return key ? t(key) : (type || '-');
  }

  function showAlert(message) {
    if (!message) {
      alertBox.classList.add('d-none');
      alertBox.textContent = '';
      return;
    }
    alertBox.textContent = message;
    alertBox.classList.remove('d-none');
  }

  function typeBadgeClass(type) {
    switch (type) {
      case 'emergency': return 'bg-danger';
      case 'inpatient': return 'bg-primary';
      default: return 'bg-secondary';
    }
  }

  function cardHtml(e) {
    const patient = escapeHtml(((e.patient_first_name || '') + ' ' + (e.patient_last_name || '')).trim());
    const doctor = escapeHtml(e.attending_name || '');
    return `
      <div class="col-md-6 col-xl-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title mb-0">${patient}</h5>
              <span class="badge ${typeBadgeClass(e.encounter_type)}">${escapeHtml(typeLabel(e.encounter_type))}</span>
            </div>
            <p class="text-muted mb-2">${t('cedula')}: ${escapeHtml(e.cedula || '-')}</p>
            <p class="mb-1"><i class="fa-solid fa-calendar me-2 text-muted"></i>${escapeHtml((e.encounter_date || '').slice(0, 16))}</p>
            <p class="mb-1"><i class="fa-solid fa-user-doctor me-2 text-muted"></i>${doctor || '-'}</p>
            <p class="mb-0"><i class="fa-solid fa-file-medical me-2 text-muted"></i>${escapeHtml(e.reason_for_visit || '-')}</p>
          </div>
          <div class="card-footer bg-transparent d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-success btn-sm btn-discharge" data-id="${e.id}">
              <i class="fa-solid fa-door-closed me-1"></i><span>${escapeHtml(t('altas_discharge_btn'))}</span>
            </button>
          </div>
        </div>
      </div>`;
  }

  async function loadOpenEncounters() {
    await waitForI18n();
    showAlert('');
    try {
      const res = await fetch('/api/encounters_list.php?status_not=closed', { credentials: 'same-origin' });
      const json = await res.json();
      if (!res.ok || !json.success) {
        throw new Error(json.error || t('error'));
      }
      const rows = Array.isArray(json.data) ? json.data : [];
      cardsRow.innerHTML = rows.map(cardHtml).join('');
      emptyBox.classList.toggle('d-none', rows.length > 0);
    } catch (err) {
      cardsRow.innerHTML = '';
      emptyBox.classList.add('d-none');
      showAlert(err.message || t('error'));
    }
  }

  cardsRow.addEventListener('click', async (event) => {
    const button = event.target.closest('.btn-discharge');
    if (!button) return;
    const id = button.dataset.id;
    if (!id) return;

    const confirmed = await swal({
      title: '',
      text: t('altas_discharge_confirm'),
      icon: 'warning',
      buttons: [t('cancel'), t('confirm_yes')],
    });
    if (!confirmed) return;

    try {
      const res = await fetch('/api/encounters_update.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: Number(id), status: 'closed', discharge_date: new Date().toISOString().slice(0, 10) })
      });
      const json = await res.json();
      if (!res.ok || !json.success) {
        throw new Error(json.error || t('error'));
      }
      swal({ title: '', text: t('altas_discharged_ok'), icon: 'success' });
      loadOpenEncounters();
    } catch (err) {
      swal({ text: err.message || t('error'), icon: 'error' });
    }
  });

  if (btnRefresh) btnRefresh.addEventListener('click', loadOpenEncounters);

  document.addEventListener('DOMContentLoaded', loadOpenEncounters);
  document.addEventListener('i18n:changed', loadOpenEncounters);
})();
</script>
