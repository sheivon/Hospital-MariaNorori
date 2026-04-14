<?php
// Auth & role guard: only allow admin users here.
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
Auth::requireRole('admin');

// Header template has common HTML head, CSS, and nav bar.
include __DIR__ . '/../../templates/header.php';
?>

<!-- Main container for the users admin page -->
<div class="container mt-4">

  <!-- Title bar + controls section -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0" data-i18n="admin_users">Admin</h3>

    <!-- Action buttons: print and create new user -->
    <div>
      <button id="btnPrintUsers" class="btn btn-secondary me-2" title="Print the user list">
        <i class="fa-solid fa-print me-1"></i>
        <span data-i18n="print">Print</span>
      </button>

      <button id="btnNewUser" class="btn btn-success" title="Open create user form">
        <i class="fa-solid fa-user-plus me-1"></i>
        <span data-i18n="create_user">Create user</span>
      </button>
    </div>
  </div>

  <!-- Data table card -->
  <div class="card">
    <div class="card-body p-2">
      <div class="table-responsive">

        <!-- Users table (data is loaded dynamically via JavaScript in /assets/js or API) -->
        <table class="table table-striped table-sm" id="usersTable" cellpadding="0" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th data-i18n="label_username">Username</th>
              <th data-i18n="label_fullname">Full name</th>
              <th data-i18n="label_cedula">Cédula</th>
              <th data-i18n="role">Role</th>
              <th data-i18n="actions" class="align-center content-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Rows are inserted here by JavaScript (e.g. fetch JSON from api/users_list.php) -->
          </tbody>
        </table>

      </div>
    </div>
  </div>
</div>

<!-- Modal markup for create/edit user form -->
<?php include __DIR__ . '/../modal/user_modal.php'; ?>

<!-- Footer template with closing tags and optional scripts -->
<?php include __DIR__ . '/../../templates/footer.php'; ?>