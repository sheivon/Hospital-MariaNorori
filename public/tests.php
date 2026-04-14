<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4" id="testsPage">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0" data-i18n="tests_title">Tests</h2>
      <p class="text-muted mb-0" data-i18n="tests_description">View test results and navigate to patients.</p>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table id="testsTable" class="table table-sm table-striped table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="test_type">Test type</th>
              <th data-i18n="patient">Patient</th>
              <th data-i18n="cedula">Cédula</th>
              <th data-i18n="result">Result</th>
              <th data-i18n="test_date">Test date</th>
              <th data-i18n="created_by">Created by</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
