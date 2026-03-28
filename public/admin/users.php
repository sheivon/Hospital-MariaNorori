<?php
require_once __DIR__ . '/../../src/auth.php';
require_role('admin');
include __DIR__ . '/../../templates/header.php';
?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="admin_users">Admin</h3>
    <div>
      <button id="btnPrintUsers" class="btn btn-secondary me-2"><i class="fa-solid fa-print me-1"></i><span data-i18n="print">Print</span></button>
      <button id="btnNewUser" class="btn btn-success"><i class="fa-solid fa-user-plus me-1"></i><span data-i18n="create_user">Create user</span></button>
    </div>
  </div>
  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-striped table-sm" id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th data-i18n="label_username">Username</th>
              <th data-i18n="label_fullname">Full name</th>
              <th data-i18n="label_cedula">Cédula</th>
              <th data-i18n="role">Role</th>
              <th data-i18n="actions" class="align-center content-center" width="6%">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../modal/user_modal.php'; ?>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
