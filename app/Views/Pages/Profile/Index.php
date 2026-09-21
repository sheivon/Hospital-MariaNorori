<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="profile-card p-4">
        <div class="row g-4 align-items-center">
          <div class="col-md-4 text-center">
            <div class="profile-avatar mb-3" id="profileAvatar" aria-hidden="true">?</div>
            <h5 id="profileName" class="h4 mb-1"><?= htmlspecialchars($profile['fullname'] ?: $profile['username'], ENT_QUOTES, 'UTF-8') ?></h5>
            <p class="text-muted" data-i18n="profile_welcome">Profile settings</p>
          </div>
          <div class="col-md-8">
            <div class="profile-section p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" data-i18n="profile_title">Profile</h5>
                <span class="badge bg-info text-dark" data-i18n="profile_role">Role:</span>
              </div>
              <p class="text-secondary mt-2"><?= htmlspecialchars($user['role'] ?? 'user', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if ($errorKey): ?>
              <div class="alert alert-danger" data-i18n="<?= htmlspecialchars($errorKey, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($errorKey, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form method="post" class="row g-3">
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_username">Username</label>
                <input class="form-control" value="<?= htmlspecialchars($profile['username'], ENT_QUOTES, 'UTF-8') ?>" disabled>
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_cedula">Cédula</label>
                <input name="cedula" class="form-control" value="<?= htmlspecialchars($profile['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_fullname">Full name</label>
                <input name="fullname" class="form-control" value="<?= htmlspecialchars($profile['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_new_password">New password (leave empty to keep)</label>
                <input name="password" type="password" class="form-control" placeholder="********">
              </div>
              <div class="col-12 text-end">
                <button class="btn btn-primary btn-acrylic">
                  <i class="fa-solid fa-floppy-disk me-1"></i><span data-i18n="btn_save">Save</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const name = <?= json_encode($profile['fullname'] ?: $profile['username']) ?>;
  const initials = name.trim().split(/\s+/).slice(0, 2).map(word => word[0]?.toUpperCase()).join('') || '?';
  const avatar = document.getElementById('profileAvatar');
  if (avatar) avatar.textContent = initials;
})();
</script>
