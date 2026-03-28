<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="userForm">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle" data-i18n="create_user">Create user</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="userFormError" class="alert alert-danger d-none"></div>
          <input type="hidden" name="id" id="userId">
          <div class="mb-3">
            <label class="form-label" data-i18n="label_username">Username</label>
            <input class="form-control" name="username" id="username">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_password">Password</label>
            <input type="password" class="form-control" name="password" id="password" data-i18n="password_optional_reset" data-i18n-placeholder="password_optional_reset">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_fullname">Full name</label>
            <input class="form-control" name="fullname" id="fullname">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_cedula">Cédula</label>
            <input class="form-control" name="cedula" id="cedula">
          </div>
          <div class="mb-3">
            <label class="form-label">Specialty</label>
            <input class="form-control" name="specialty" id="specialty">
          </div>
          <div class="mb-3">
            <label class="form-label">Department</label>
            <input class="form-control" name="department" id="department">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="role">Role</label>
            <select class="form-select" name="role" id="role"></select>
          </div>
          <small class="text-muted" data-i18n="password_optional_reset">Leave password empty to keep current (when editing)</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="Close">Close</button>
          <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i><span data-i18n="save">Save</span></button>
        </div>
      </form>
    </div>
  </div>
</div>
