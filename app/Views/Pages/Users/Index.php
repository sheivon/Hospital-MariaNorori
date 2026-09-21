<?php $GLOBALS['PAGE_SCRIPTS'][] = 'datatables'; $GLOBALS['PAGE_SCRIPTS'][] = 'users'; ?>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="admin_users">Admin</h3>
    <div> 
      <button id="btnNewUser" class="btn btn-success" title="Open create user form">
        <i class="fa-solid fa-user-plus me-1"></i>
        <span data-i18n="create_user">Create user</span>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">
        <table class="table table-striped table-sm dt-container" id="usersTable" cellpadding="0" width="100%">
          <thead>
<tr>
              <th>ID</th>
              <th data-i18n="label_username">Username</th>
              <th data-i18n="label_fullname">Full name</th>
              <th data-i18n="label_cedula">Cédula</th>
              <th data-i18n="role">Role</th>
              <th data-i18n="label_specialty">Specialty</th>
              <th data-i18n="label_department">Department</th>
              <th data-i18n="table_status">Status</th>
              <th data-i18n="created_at">Created At</th>
              <th data-i18n="actions" class="align-center content-center">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include APP_ROOT . '/app/Views/Shared/Modals/user_modal.php'; ?>

<?php include APP_ROOT . '/templates/loading_overlay.php'; ?>
