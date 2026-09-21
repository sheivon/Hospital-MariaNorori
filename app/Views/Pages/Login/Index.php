<?php if (empty($_SESSION['user'])): ?>
  <div class="auth-screen d-flex align-items-center justify-content-center vh-100">
    <div class="card card-glass shadow-lg" style="width:420px;max-width:92vw; margin: 0 12px;">
      <div class="card-body p-4">
        <div class="text-center mb-3">
          <picture class="login-logo">
            <source srcset="/assets/images/minsa-logo.svg" type="image/svg+xml">
            <img src="/assets/images/minsa-logo.svg" alt="MINSA_logo" height="60">
          </picture>
          <h3 class="mt-2 mb-0"><i class="fa-solid fa-right-to-bracket me-2"></i><span data-i18n="login_title">Login</span></h3>
        </div>
        <?php if ($errorKey): ?>
          <div class="alert alert-danger" data-i18n="<?= htmlspecialchars($errorKey, ENT_QUOTES, 'UTF-8') ?>">Invalid username or password</div>
        <?php endif; ?>
        <?php if (!$hasUsers): ?>
          <div class="alert alert-warning">No active users found. Run setup and create default users, then log in with admin/admin123.</div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label" data-i18n="label_username">Username</label>
            <input name="username" class="form-control" required data-i18n="label_username" data-i18n-placeholder="label_username">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_password">Password</label>
            <input name="password" type="password" class="form-control" required data-i18n="label_password" data-i18n-placeholder="label_password">
          </div>
          <button class="btn btn-primary w-100"><i class="fa-solid fa-right-to-bracket me-1"></i><span data-i18n="btn_login">Login</span></button>
        </form>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="alert alert-info text-center mt-3">
    <p data-i18n="already_logged_in">You are already logged in as</p>
    <strong><?= htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') ?></strong>.
    <a href="/patients.php" class="btn btn-sm btn-primary ms-2"><i class="fa-solid fa-users me-1"></i><span data-i18n="go_patients">Ir al panel de pacientes</span></a>
    <a href="/logout.php" class="btn btn-sm btn-secondary ms-2"><i class="fa-solid fa-right-from-bracket me-1"></i><span data-i18n="logout">Logout</span></a>
  </div>
<?php endif; ?>

<script src="/assets/js/jquery-3.6.0.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>
<script src="/assets/js/i18n.js"></script>
<script src="/assets/js/app.js"></script>
