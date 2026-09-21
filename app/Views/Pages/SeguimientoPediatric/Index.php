<?php $GLOBALS['PAGE_SCRIPTS'][] = 'datatables'; ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fa-solid fa-child me-2"></i>Seguimiento Pediátrico V2</h2>
    <button class="btn btn-success" id="btnAddPediatricVisit">
      <i class="fa-solid fa-plus me-1"></i>Nuevo
    </button>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
<table class="table table-sm table-striped dt-container" id="pediatricTable">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="patient">Paciente</th>
              <th data-i18n="seguimiento_visit_date">Fecha de Visita</th>
              <th data-i18n="actions">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="pediatricModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <!-- Modal Content -->
    <style>
        .nav-tabs .nav-link { margin-bottom: -1px; }
        .tab-content { background: #fff; }
    </style>
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pediatricModalTitle">Visita Pediátrica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="pediatricAlert" class="alert alert-danger d-none"></div>
        <form id="pediatricForm">
          <input type="hidden" id="pId" name="id">
          
          <div class="row g-3 mb-3">
            <div class="col-md-6">
                <!-- Using patientsListModal from existing code for patient selection -->
                <label class="form-label" for="patientDisplay">Paciente *</label>
                <div class="input-group">
                    <input id="patientDisplay" class="form-control" readonly required placeholder="Seleccione paciente">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#patientsListModal">Seleccionar</button>
                </div>
                <input type="hidden" id="pPatientId" name="patient_id">
            </div>
            
            <div class="col-md-6">
              <label for="pVisitDate" class="form-label">Fecha de Visita *</label>
              <input type="date" class="form-control" id="pVisitDate" name="visit_date" required>
            </div>
          </div>

          <!-- Tabs for Sections -->
          <ul class="nav nav-tabs" id="pediatricTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="historia-tab" data-bs-toggle="tab" data-bs-target="#historia" type="button" role="tab" aria-controls="historia" aria-selected="true">Historia Clínica</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="signos-tab" data-bs-toggle="tab" data-bs-target="#signos" type="button" role="tab" aria-controls="signos" aria-selected="false">Signos de Alarma</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="fisico-tab" data-bs-toggle="tab" data-bs-target="#fisico" type="button" role="tab" aria-controls="fisico" aria-selected="false">Examen Físico/Nutrición</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="alimentacion-tab" data-bs-toggle="tab" data-bs-target="#alimentacion" type="button" role="tab" aria-controls="alimentacion" aria-selected="false">Alimentación y Vacunas</button>
            </li>
          </ul>

          <div class="tab-content border border-top-0 p-3" id="pediatricTabsContent">
            <!-- Historia Clínica -->
            <div class="tab-pane fade show active" id="historia" role="tabpanel" aria-labelledby="historia-tab">
              <div class="row g-3">
                  <div class="col-md-12">
                      <label for="pReason" class="form-label">Motivo de consulta</label>
                      <textarea class="form-control" id="pReason" name="reason_for_consultation" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Antecedentes Personales Patológicos</label>
                      <textarea class="form-control" id="pPersonalPathological" name="personal_pathological_history" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Factores de Riesgo</label>
                      <textarea class="form-control" id="pRiskFactors" name="risk_factors" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Antecedentes Familiares Patológicos</label>
                      <textarea class="form-control" id="pFamilyPathological" name="family_pathological_history" rows="2"></textarea>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Entorno Familiar</label>
                      <textarea class="form-control" id="pFamilyEnvironment" name="family_environment" rows="2"></textarea>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label">Educación/Trabajo/Vivienda</label>
                      <textarea class="form-control" id="pEducation" name="education_work_living" rows="2"></textarea>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label">Actividades Sociales</label>
                      <textarea class="form-control" id="pActivitiesSocial" name="activities_social" rows="2"></textarea>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label">Actividad Física</label>
                      <textarea class="form-control" id="pPhysicalActivity" name="physical_activity" rows="2"></textarea>
                  </div>
              </div>
            </div>

            <!-- Signos de Alarma -->
            <div class="tab-pane fade" id="signos" role="tabpanel" aria-labelledby="signos-tab">
              <div class="row g-3">
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pRespiraRapida" name="respira_rapida"><label class="form-check-label" for="pRespiraRapida">Respira rápida</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pDificultadAlimentarse" name="dificultad_alimentarse"><label class="form-check-label" for="pDificultadAlimentarse">Dificultad alimentarse</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pDificultadRespirar" name="dificultad_respirar"><label class="form-check-label" for="pDificultadRespirar">Dificultad respirar</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pConvulsiones" name="convulsiones"><label class="form-check-label" for="pConvulsiones">Convulsiones</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pLetargia" name="letargia"><label class="form-check-label" for="pLetargia">Letargia</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pInconciencia" name="inconciencia"><label class="form-check-label" for="pInconciencia">Inconciencia</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pFlacidez" name="flacidez"><label class="form-check-label" for="pFlacidez">Flacidez</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pVomitos" name="vomitos"><label class="form-check-label" for="pVomitos">Vómitos</label></div></div>
                  
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pDiarrea" name="diarrea"><label class="form-check-label" for="pDiarrea">Diarrea</label></div></div>
                  <div class="col-md-3"><label class="form-label mb-0"><small>Días de diarrea</small></label><input type="number" class="form-control form-control-sm" id="pDiasDiarrea" name="dias_diarrea" min="0"></div>
                  
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pFiebre" name="fiebre"><label class="form-check-label" for="pFiebre">Fiebre</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pFiebreMas7" name="fiebre_mas_7_dias"><label class="form-check-label" for="pFiebreMas7">Fiebre >7 días</label></div></div>
                  
                  <!-- Other Signs -->
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pCianosis" name="cianosis_central"><label class="form-check-label" for="pCianosis">Cianosis central</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pOmbligoRo" name="ombligo_rojizo"><label class="form-check-label" for="pOmbligoRo">Ombligo rojizo</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pOmbligoSu" name="ombligo_supurando"><label class="form-check-label" for="pOmbligoSu">Ombligo supurando</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pPustulasEx" name="pustulas_extensas"><label class="form-check-label" for="pPustulasEx">Pústulas extensas</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pPustulasEs" name="pustulas_escasas"><label class="form-check-label" for="pPustulasEs">Pústulas escasas</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pTiraje" name="tiraje_subcostal"><label class="form-check-label" for="pTiraje">Tiraje subcostal</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pPlacas" name="placas_blancas_bucales"><label class="form-check-label" for="pPlacas">Placas blancas buc</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pHipotermia" name="hipotermia"><label class="form-check-label" for="pHipotermia">Hipotermia</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSeVeMal" name="se_ve_mal"><label class="form-check-label" for="pSeVeMal">Se ve mal</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSupOido" name="supuracion_oido"><label class="form-check-label" for="pSupOido">Supuracion oido</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSupOjos" name="supuracion_ojos"><label class="form-check-label" for="pSupOjos">Supuracion ojos</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pManifSangrado" name="manifestacion_sangrado"><label class="form-check-label" for="pManifSangrado">Manif sangrado</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pDistension" name="distension_abdominal"><label class="form-check-label" for="pDistension">Distensión abd</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pApnea" name="apnea"><label class="form-check-label" for="pApnea">Apnea</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pQuejido" name="quejido"><label class="form-check-label" for="pQuejido">Quejido</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pAleteo" name="aleteo_nasal"><label class="form-check-label" for="pAleteo">Aleteo nasal</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pPalidez" name="palidez_intensa"><label class="form-check-label" for="pPalidez">Palidez intensa</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pLlenado" name="llenado_capilar_lento"><label class="form-check-label" for="pLlenado">Llenado cap lento</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pFontanela" name="fontanela_abombada"><label class="form-check-label" for="pFontanela">Fontanela abombada</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSangHeces" name="sangrado_heces"><label class="form-check-label" for="pSangHeces">Sangrado en heces</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSomnoliento" name="anormalmente_somnoliento"><label class="form-check-label" for="pSomnoliento">Somnoliento</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pOjosHun" name="ojos_hundidos"><label class="form-check-label" for="pOjosHun">Ojos hundidos</label></div></div>
                  <div class="col-md-3"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pIrritable" name="inquieto_irritable"><label class="form-check-label" for="pIrritable">Inquieto/Irritable</label></div></div>
              </div>
            </div>

            <!-- Físico / Nutrición -->
            <div class="tab-pane fade" id="fisico" role="tabpanel" aria-labelledby="fisico-tab">
              <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label mb-0">Peso (g)</label>
                    <input type="number" class="form-control mx-attr" id="pPesoG" name="peso_g">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label mb-0">Talla (cm)</label>
                    <input type="number" step="0.01" class="form-control mx-attr" id="pTalla" name="talla_cm">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label mb-0">Perímetro Cefálico (cm)</label>
                    <input type="number" step="0.01" class="form-control mx-attr" id="pPerimetro" name="perimetro_cefalico_cm">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label mb-0">IMC</label>
                    <input type="number" step="0.01" class="form-control mx-attr" id="pImc" name="imc">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label mb-0">Peso/Edad</label>
                    <select class="form-select mx-attr" id="pPesoEdad" name="peso_edad">
                      <option value="normal">Normal</option>
                      <option value="bajo">Bajo</option>
                      <option value="alto">Alto</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label mb-0">Talla/Edad</label>
                    <select class="form-select mx-attr" id="pTallaEdad" name="talla_edad">
                      <option value="normal">Normal</option>
                      <option value="bajo">Bajo</option>
                      <option value="alto">Alto</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label mb-0">Peso/Talla</label>
                    <select class="form-select mx-attr" id="pPesoTalla" name="peso_talla">
                      <option value="normal">Normal</option>
                      <option value="bajo">Bajo</option>
                      <option value="alto">Alto</option>
                    </select>
                  </div>

                  <div class="col-12 mt-4"><h5>Signos Físicos Adicionales</h5></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pEdemaPies" name="edema_pies"><label class="form-check-label" for="pEdemaPies">Edema de pies</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pEmaciacion" name="emaciacion"><label class="form-check-label" for="pEmaciacion">Emaciación visible grave</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pMalnutricion" name="malnutricion"><label class="form-check-label" for="pMalnutricion">Malnutrición severa</label></div></div>
              </div>
            </div>

            <!-- Alimentación y Vacunas -->
            <div class="tab-pane fade" id="alimentacion" role="tabpanel" aria-labelledby="alimentacion-tab">
              <div class="row g-3">
                  <div class="col-12"><h5>Lactancia y Alimentación</h5></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pLactanciaMat" name="lactancia_materna"><label class="form-check-label" for="pLactanciaMat">Lactancia Materna Exclusiva</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pLactanciaNoc" name="lactancia_nocturna"><label class="form-check-label" for="pLactanciaNoc">Lactancia nocturna</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pLactanciaMas8" name="lactancia_mas_8_veces"><label class="form-check-label" for="pLactanciaMas8">Lactancia >8 veces al día</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pOtrosLiq" name="otros_liquidos"><label class="form-check-label" for="pOtrosLiq">Recibe otros líquidos/alimentos</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pBiberon" name="uso_biberon"><label class="form-check-label" for="pBiberon">Usa biberón/pepe</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pPosicion" name="problemas_posicion"><label class="form-check-label" for="pPosicion">Problemas de posición al amamantar</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pAgarre" name="problemas_agarre"><label class="form-check-label" for="pAgarre">Problemas de agarre</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pSuccion" name="problemas_succion"><label class="form-check-label" for="pSuccion">Problemas de succión</label></div></div>

                  <div class="col-12 mt-4"><h5>Vacunas</h5></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pVacuna" name="vacuna"><label class="form-check-label" for="pVacuna">Tiene tarjeta de vacunas</label></div></div>
                  <div class="col-md-4"><div class="form-check"><input class="form-check-input p-chk" type="checkbox" id="pVacunaEdad" name="vacuna_edad"><label class="form-check-label" for="pVacunaEdad">Vacunas de acuerdo a edad</label></div></div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnSavePediatric">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?php include APP_ROOT . '/app/Views/Shared/Modals/patient_list_modal.php'; ?>

<script src="/assets/js/seguimiento_pediatric_data.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tblBody = document.querySelector('#pediatricTable tbody');
  const alertBox = document.getElementById('pediatricAlert');
  const form = document.getElementById('pediatricForm');
  const modalEl = document.getElementById('pediatricModal');
  const modal = new bootstrap.Modal(modalEl);
  let datatable = null;

  async function loadData() {
      try {
          const res = await SeguimientoPediatricDataLayer.list();
          renderTable(res.rows || []);
      } catch(e) {
          console.error(e);
      }
  }

function escapeHtml(s){ return (s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function renderTable(rows) {
      if (datatable) {
        $('#pediatricTable').DataTable().destroy();
      }
      tblBody.innerHTML = rows.map((r, i) => `
        <tr>
            <td>${i + 1}</td>
            <td>${escapeHtml(r.patient_name || t('unknown') || 'Desconocido')}</td>
            <td>${escapeHtml(r.visit_date || '')}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary btn-edit table-action-btn" data-id="${escapeHtml(r.id)}" title="${escapeHtml(t('edit') || 'Edit')}"><i class="fa-solid fa-pen-to-square"></i><span class="btn-label">${escapeHtml(t('edit') || 'Edit')}</span></button>
                <button class="btn btn-sm btn-danger btn-del table-action-btn" data-id="${escapeHtml(r.id)}" title="${escapeHtml(t('delete') || 'Delete')}"><i class="fa-solid fa-trash"></i><span class="btn-label">${escapeHtml(t('delete') || 'Delete')}</span></button>
            </td>
        </tr>
      `).join('');

      datatable = $('#pediatricTable').DataTable({
      layout: {
        topStart: {
          buttons: [
            { extend: 'copy', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'csv', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'excel', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', exportOptions: { columns: ':not(:last-child)' } },
            {
              extend: 'print',
              exportOptions: { columns: ':not(:last-child)' },
              action: function () {
                if (typeof window.triggerCustomPrint === 'function') {
                  window.triggerCustomPrint('seguimiento_pediatric');
                  return;
                }
                window.open('/print.php?resource=seguimiento_pediatric', '_blank');
              }
            },
            { extend: 'colvis' }
          ]
        }
      },
      responsive: true,
      autoWidth: false,
      scrollX: false });
  }

  window.selectPatientFromModal = function(id, name) {
      document.getElementById('pPatientId').value = id;
      document.getElementById('patientDisplay').value = name;
      bootstrap.Modal.getOrCreateInstance(document.getElementById('patientsListModal')).hide();
  };

  document.getElementById('btnAddPediatricVisit').addEventListener('click', () => {
      form.reset();
      document.getElementById('pId').value = '';
      document.getElementById('pPatientId').value = '';
      document.getElementById('patientDisplay').value = '';
      alertBox.classList.add('d-none');
      document.getElementById('pediatricModalTitle').textContent = 'Nueva Visita';
      modal.show();
  });

  document.getElementById('btnSavePediatric').addEventListener('click', async () => {
      const pid = document.getElementById('pId').value;
      const patientId = document.getElementById('pPatientId').value;
      const visitDate = document.getElementById('pVisitDate').value;
      
      if (!patientId || !visitDate) {
          alertBox.textContent = 'Paciente y Fecha son requeridos';
          alertBox.classList.remove('d-none');
          return;
      }

      // Gather text inputs
      const payload = {
          patient_id: patientId,
          visit_date: visitDate,
          reason_for_consultation: document.getElementById('pReason').value,
          personal_pathological_history: document.getElementById('pPersonalPathological').value,
          risk_factors: document.getElementById('pRiskFactors').value,
          family_pathological_history: document.getElementById('pFamilyPathological').value,
          family_environment: document.getElementById('pFamilyEnvironment').value,
          education_work_living: document.getElementById('pEducation').value,
          activities_social: document.getElementById('pActivitiesSocial').value,
          physical_activity: document.getElementById('pPhysicalActivity').value,
          peso_g: document.getElementById('pPesoG').value || null,
          talla_cm: document.getElementById('pTalla').value || null,
          perimetro_cefalico_cm: document.getElementById('pPerimetro').value || null,
          imc: document.getElementById('pImc').value || null,
          peso_edad: document.getElementById('pPesoEdad').value || 'normal',
          talla_edad: document.getElementById('pTallaEdad').value || 'normal',
          peso_talla: document.getElementById('pPesoTalla').value || 'normal',
          dias_diarrea: document.getElementById('pDiasDiarrea').value || 0
      };

      // Gather all checkbox booleans dynamically by class "p-chk"
      document.querySelectorAll('.p-chk').forEach(chk => {
          payload[chk.name] = chk.checked ? 1 : 0;
      });

      try {
          if (pid) {
              payload.id = pid;
              await SeguimientoPediatricDataLayer.update(payload);
          } else {
              await SeguimientoPediatricDataLayer.create(payload);
          }
          modal.hide();
          loadData();
      } catch (e) {
          alertBox.textContent = e.message;
          alertBox.classList.remove('d-none');
      }
  });

  tblBody.addEventListener('click', async (e) => {
      const editBtn = e.target.closest('.btn-edit');
      const delBtn = e.target.closest('.btn-del');

      if (editBtn) {
          try {
              const res = await SeguimientoPediatricDataLayer.get(editBtn.dataset.id);
              const r = res.row;
              form.reset();
              document.getElementById('pId').value = r.id;
              document.getElementById('pPatientId').value = r.patient_id;
              document.getElementById('patientDisplay').value = r.patient_name || '';
              document.getElementById('pVisitDate').value = r.visit_date;
              document.getElementById('pReason').value = r.reason_for_consultation || '';
              document.getElementById('pPersonalPathological').value = r.personal_pathological_history || '';
              document.getElementById('pRiskFactors').value = r.risk_factors || '';
              document.getElementById('pFamilyPathological').value = r.family_pathological_history || '';
              document.getElementById('pFamilyEnvironment').value = r.family_environment || '';
              document.getElementById('pEducation').value = r.education_work_living || '';
              document.getElementById('pActivitiesSocial').value = r.activities_social || '';
              document.getElementById('pPhysicalActivity').value = r.physical_activity || '';
              document.getElementById('pPesoG').value = r.peso_g || '';
              document.getElementById('pTalla').value = r.talla_cm || '';
              document.getElementById('pPerimetro').value = r.perimetro_cefalico_cm || '';
              document.getElementById('pImc').value = r.imc || '';
              document.getElementById('pPesoEdad').value = r.peso_edad || 'normal';
              document.getElementById('pTallaEdad').value = r.talla_edad || 'normal';
              document.getElementById('pPesoTalla').value = r.peso_talla || 'normal';
              document.getElementById('pDiasDiarrea').value = r.dias_diarrea || '';

              // Set all booleans dynamically
              document.querySelectorAll('.p-chk').forEach(chk => {
                  chk.checked = !!parseInt(r[chk.name] || 0);
              });

              document.getElementById('pediatricModalTitle').textContent = 'Editar Visita';
              modal.show();
          } catch(e) {
              console.error(e);
          }
      }

      if (delBtn) {
          if (confirm('¿Eliminar registro?')) {
              await SeguimientoPediatricDataLayer.delete(delBtn.dataset.id);
              loadData();
          }
      }
  });

  loadData();
});
</script>


