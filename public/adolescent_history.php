<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4" id="adolescentHistoryPage">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0" data-i18n="adolescent_history_title">Adolescent History</h2>
    <p class="text-muted mb-0" data-i18n="adolescent_history_description">Capture adolescent clinical history and social context.</p>
  </div>

  <div class="row">
    <div class="col-lg-7">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0" data-i18n="adolescent_history_list_title">Adolescent History Records</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="adolescentHistoryTable" class="table table-sm table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th data-i18n="patient">Patient</th>
                  <th data-i18n="cedula">Cédula</th>
                  <th data-i18n="visit_date">Visit Date</th>
                  <th data-i18n="reason_for_consultation">Reason</th>
                  <th data-i18n="created_at">Created</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0" data-i18n="add_adolescent_history">Add Clinical History</h5>
        </div>
        <div class="card-body">
          <div id="adolescentHistoryError" class="d-none mb-3"></div>
          <form id="adolescentHistoryForm">
            <div class="mb-3">
              <label for="patient_id" class="form-label" data-i18n="patient">Patient</label>
              <select id="patient_id" name="patient_id" class="form-select" required></select>
            </div>
            <div class="mb-3">
              <label for="visit_date" class="form-label" data-i18n="visit_date">Visit Date</label>
              <input type="date" id="visit_date" name="visit_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="reason_for_consultation" class="form-label" data-i18n="reason_for_consultation">Reason for Consultation</label>
              <textarea id="reason_for_consultation" name="reason_for_consultation" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="personal_pathological_history" class="form-label" data-i18n="personal_pathological_history">Personal Pathological History</label>
              <textarea id="personal_pathological_history" name="personal_pathological_history" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label" data-i18n="personal_risk_factors_section">Personal / Risk Factors</label>
              <div class="row g-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_tobacco" name="personal_risk_factors[]" value="tobacco">
                    <label class="form-check-label" for="risk_tobacco" data-i18n="risk_tobacco">Tobacco</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_alcohol" name="personal_risk_factors[]" value="alcohol">
                    <label class="form-check-label" for="risk_alcohol" data-i18n="risk_alcohol">Alcohol</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_drugs" name="personal_risk_factors[]" value="drugs">
                    <label class="form-check-label" for="risk_drugs" data-i18n="risk_drugs">Drugs</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_medications" name="personal_risk_factors[]" value="medications">
                    <label class="form-check-label" for="risk_medications" data-i18n="risk_medications">Medications</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_surgeries" name="personal_risk_factors[]" value="surgeries">
                    <label class="form-check-label" for="risk_surgeries" data-i18n="risk_surgeries">Surgeries</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_chronic_illness" name="personal_risk_factors[]" value="chronic_illness">
                    <label class="form-check-label" for="risk_chronic_illness" data-i18n="risk_chronic_illness">Chronic Illness</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="risk_hospitalizations" name="personal_risk_factors[]" value="hospitalizations">
                    <label class="form-check-label" for="risk_hospitalizations" data-i18n="risk_hospitalizations">Hospitalizations</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" data-i18n="family_history_section">Family Pathological / Risk History</label>
              <div class="row g-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_diabetes" name="family_risk_factors[]" value="diabetes">
                    <label class="form-check-label" for="family_diabetes" data-i18n="family_diabetes">Diabetes</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_hypertension" name="family_risk_factors[]" value="hypertension">
                    <label class="form-check-label" for="family_hypertension" data-i18n="family_hypertension">Hypertension</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_asthma" name="family_risk_factors[]" value="asthma">
                    <label class="form-check-label" for="family_asthma" data-i18n="family_asthma">Asthma</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_cancer" name="family_risk_factors[]" value="cancer">
                    <label class="form-check-label" for="family_cancer" data-i18n="family_cancer">Cancer</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_allergies" name="family_risk_factors[]" value="allergies">
                    <label class="form-check-label" for="family_allergies" data-i18n="family_allergies">Allergies</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="family_covid" name="family_risk_factors[]" value="covid">
                    <label class="form-check-label" for="family_covid" data-i18n="family_covid">COVID-19</label>
                  </div>
                </div>
              </div>
              <div class="mt-2">
                <label for="family_other_conditions" class="form-label" data-i18n="family_other_conditions">Other Conditions</label>
                <input type="text" id="family_other_conditions" name="family_other_conditions" class="form-control">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" data-i18n="family_environment_section">Family / Social Environment</label>
              <div class="row g-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lives_with_mother" name="family_environment_options[]" value="mother">
                    <label class="form-check-label" for="lives_with_mother" data-i18n="lives_with_mother">Mother</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lives_with_father" name="family_environment_options[]" value="father">
                    <label class="form-check-label" for="lives_with_father" data-i18n="lives_with_father">Father</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lives_with_both" name="family_environment_options[]" value="both">
                    <label class="form-check-label" for="lives_with_both" data-i18n="lives_with_both">Both</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lives_with_guardian" name="family_environment_options[]" value="guardian">
                    <label class="form-check-label" for="lives_with_guardian" data-i18n="lives_with_guardian">Guardian</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="housing_electricity" name="family_environment_options[]" value="electricity">
                    <label class="form-check-label" for="housing_electricity" data-i18n="housing_electricity">Electricity</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="housing_running_water" name="family_environment_options[]" value="running_water">
                    <label class="form-check-label" for="housing_running_water" data-i18n="housing_running_water">Running Water</label>
                  </div>
                </div>
              </div>
              <div class="row g-2 mt-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="domestic_violence" name="family_environment_options[]" value="domestic_violence">
                    <label class="form-check-label" for="domestic_violence" data-i18n="domestic_violence">Domestic Violence</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="judicial_issues" name="family_environment_options[]" value="judicial_issues">
                    <label class="form-check-label" for="judicial_issues" data-i18n="judicial_issues">Judicial Issues</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label" data-i18n="education_section">Education, Social and Work Activities</label>
              <div class="row g-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="education_primary" name="education_options[]" value="primary">
                    <label class="form-check-label" for="education_primary" data-i18n="education_primary">Primary</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="education_secondary" name="education_options[]" value="secondary">
                    <label class="form-check-label" for="education_secondary" data-i18n="education_secondary">Secondary</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="education_technical" name="education_options[]" value="technical">
                    <label class="form-check-label" for="education_technical" data-i18n="education_technical">Technical</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="education_university" name="education_options[]" value="university">
                    <label class="form-check-label" for="education_university" data-i18n="education_university">University</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="work_yes" name="education_options[]" value="works">
                    <label class="form-check-label" for="work_yes" data-i18n="work_yes">Works</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="activity_sports" name="education_options[]" value="sports">
                    <label class="form-check-label" for="activity_sports" data-i18n="activity_sports">Sports / Physical Activity</label>
                  </div>
                </div>
              </div>
              <div class="mt-2">
                <label for="screen_time" class="form-label" data-i18n="screen_time_hours">Screen Time / Hours per day</label>
                <input type="text" id="screen_time" name="screen_time" class="form-control">
              </div>
            </div>
            <div class="mb-3">
              <label for="family_pathological_history" class="form-label" data-i18n="family_pathological_history">Family Pathological History</label>
              <textarea id="family_pathological_history" name="family_pathological_history" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="family_environment" class="form-label" data-i18n="family_environment">Family / Social Environment</label>
              <textarea id="family_environment" name="family_environment" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="education_work_living" class="form-label" data-i18n="education_work_living">Education / Work / Living</label>
              <textarea id="education_work_living" name="education_work_living" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="activities_social" class="form-label" data-i18n="activities_social">Activities / Social Life</label>
              <textarea id="activities_social" name="activities_social" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="physical_activity" class="form-label" data-i18n="physical_activity">Physical Activity / Lifestyle</label>
              <textarea id="physical_activity" name="physical_activity" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="notes" class="form-label" data-i18n="notes">Notes</label>
              <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" data-i18n="save">Save</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
