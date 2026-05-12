<div class="mt-4 print-container">
  <div class="print-header d-flex align-items-center mb-3">
    <img src="/assets/images/Logo-01.png" alt="Logo" style="max-height: 70px; margin-right: 1rem;" />
    <div>
      <h1 id="printPageTitle">Seguimiento: Información para impresión</h1>
      <p id="printPageSubtitle" class="text-muted">Cargando información del seguimiento...</p>
    </div>
  </div>

  <div class="card mb-3" id="printStatus" style="display:none;">
    <div class="card-body">
      <p id="printStatusText">Cargando...</p>
    </div>
  </div>

  <div id="followupPrintContent" style="display:none;">
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Datos generales</h5>
        <dl class="row">
          <dt class="col-sm-3">ID de seguimiento</dt>
          <dd class="col-sm-9" id="followupId"></dd>

          <dt class="col-sm-3">Paciente</dt>
          <dd class="col-sm-9" id="followupPatient"></dd>

          <dt class="col-sm-3">Cédula</dt>
          <dd class="col-sm-9" id="followupCedula"></dd>

          <dt class="col-sm-3">Fecha de visita</dt>
          <dd class="col-sm-9" id="followupVisitDate"></dd>

          <dt class="col-sm-3">Encuentro</dt>
          <dd class="col-sm-9" id="followupEncounter"></dd>
        </dl>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Signos principales</h5>
        <div id="followupSigns" class="row"></div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Nutrición y crecimiento</h5>
        <dl class="row">
          <dt class="col-sm-3">Peso (g)</dt>
          <dd class="col-sm-9" id="followupPeso"></dd>

          <dt class="col-sm-3">Talla (cm)</dt>
          <dd class="col-sm-9" id="followupTalla"></dd>

          <dt class="col-sm-3">Perímetro cefálico (cm)</dt>
          <dd class="col-sm-9" id="followupPerimetro"></dd>

          <dt class="col-sm-3">IMC</dt>
          <dd class="col-sm-9" id="followupImc"></dd>

          <dt class="col-sm-3">Peso/edad</dt>
          <dd class="col-sm-9" id="followupPesoEdad"></dd>

          <dt class="col-sm-3">Talla/edad</dt>
          <dd class="col-sm-9" id="followupTallaEdad"></dd>

          <dt class="col-sm-3">Peso/talla</dt>
          <dd class="col-sm-9" id="followupPesoTalla"></dd>
        </dl>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Vacunas y suplementos</h5>
        <div id="followupImmunizations" class="row"></div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Entorno familiar</h5>
        <div id="followupFamily" class="row"></div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="mb-3">Notas</h5>
        <p id="followupNotesText"></p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Notas asociadas</h5>
        <ul class="list-group" id="followupNotesList"></ul>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  const status = document.getElementById('printStatus');
  const statusText = document.getElementById('printStatusText');
  const content = document.getElementById('followupPrintContent');

  const showError = (message) => {
    status.style.display = 'block';
    statusText.textContent = message;
    content.style.display = 'none';
  };

  const booleanLabel = (value) => value ? 'Sí' : 'No';
  const renderBoolItems = (containerId, items) => {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = items.map(item => `
      <div class="col-sm-4 mb-2">
        <div class="border rounded-2 p-2 bg-light">
          <strong>${item.label}</strong><br />${booleanLabel(item.value)}
        </div>
      </div>
    `).join('');
  };

  const fillValue = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '';
  };

  if (!id || !Number.isInteger(Number(id))) {
    showError('ID de seguimiento inválido');
    return;
  }

  fetch(`/api/child_followups_list.php?id=${encodeURIComponent(id)}`, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(json => {
      if (!json.success) {
        showError(json.error || 'Error al cargar el seguimiento');
        return;
      }
      const row = Array.isArray(json.data) ? json.data[0] : null;
      if (!row) {
        showError('Seguimiento no encontrado');
        return;
      }

      fillValue('followupId', row.id);
      fillValue('followupPatient', `${row.patient_first_name || ''} ${row.patient_last_name || ''}`.trim());
      fillValue('followupCedula', row.cedula || '');
      fillValue('followupVisitDate', row.visit_date || '');
      fillValue('followupEncounter', row.encounter_id || 'N/A');
      fillValue('followupPeso', row.peso_g ?? '');
      fillValue('followupTalla', row.talla_cm ?? '');
      fillValue('followupPerimetro', row.perimetro_cefalico_cm ?? '');
      fillValue('followupImc', row.imc ?? '');
      fillValue('followupPesoEdad', row.peso_edad || '');
      fillValue('followupTallaEdad', row.talla_edad || '');
      fillValue('followupPesoTalla', row.peso_talla || '');
      fillValue('followupNotesText', row.notas || 'Sin notas adicionales');

      renderBoolItems('followupSigns', [
        { label: 'Respira rápida', value: row.respira_rapida },
        { label: 'Dificultad alimentarse', value: row.dificultad_alimentarse },
        { label: 'Dificultad respirar', value: row.dificultad_respirar },
        { label: 'Convulsiones', value: row.convulsiones },
        { label: 'Fiebre', value: row.fiebre },
        { label: 'Malnutrición', value: row.malnutricion },
      ]);

      renderBoolItems('followupImmunizations', [
        { label: 'Vacuna', value: row.vacuna },
        { label: 'Vitamina A', value: row.vitamina_a },
        { label: 'Hierro', value: row.hierro },
        { label: 'Zinc', value: row.zinc },
        { label: 'Antiparasitario', value: row.antiparasitario },
      ]);

      renderBoolItems('followupFamily', [
        { label: 'Buen trato', value: row.buen_trato },
        { label: 'Lesiones físicas', value: row.lesiones_fisicas },
        { label: 'Lesiones genitales', value: row.lesiones_genitales },
        { label: 'Lesiones ano', value: row.lesiones_ano },
        { label: 'Comportamiento alterado', value: row.comportamiento_alterado },
        { label: 'Comportamiento cuidador alterado', value: row.comportamiento_cuidador_alterado },
      ]);

      status.style.display = 'none';
      content.style.display = 'block';
      setTimeout(() => window.print(), 300);
    })
    .catch((error) => {
      showError(error.message || 'Error al cargar datos');
    });
})();
</script>
