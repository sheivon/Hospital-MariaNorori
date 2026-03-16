<?php
require_once __DIR__ . '/../src/auth.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
  if (login($u, $p)) {
    // When serving from the `public/` directory as the web root,
    // redirect to `/` (document root) instead of `/public/` which
    // would resolve to `/public/` under the web root and cause 404s.
    header('Location: /');
    exit;
  } else {
        $err = 'invalid_login';
    }
}
include __DIR__ . '/../templates/login-header.php';
?>
<?php if (empty($_SESSION['user'])): ?>
  <div class="auth-screen d-flex align-items-center justify-content-center vh-100">
    <div class="card card-glass shadow-lg" style="width:420px;max-width:92vw; margin: 0 12px;">
      <div class="card-body p-4">
        <div class="text-center mb-3">
              <picture class="login-logo">
                <!-- prefer the SVG; PNG was converted to an embedded-SVG file -->
                <source srcset="/assets/images/minsa-logo.svg" type="image/svg+xml">
                <img src="/assets/images/minsa-logo.svg" alt="MINSA_logo" height="40" />
              </picture>
          <h3 class="mt-2 mb-0"><i class="fa-solid fa-right-to-bracket me-2"></i><span data-i18n="login_title">Login</span></h3>
        </div>
        <?php if (!empty($err)): ?>
          <div class="alert alert-danger" data-i18n="<?= htmlspecialchars($err) ?>">Invalid username or password</div>
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
    <p class="" data-i18n="already_logged_in">You are already logged in as </p>
    <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>.
    <a href="/patients.php" class="btn btn-sm btn-primary ms-2"><i class="fa-solid fa-users me-1"></i><span data-i18n="go_patients">Go to Patients Dashboard</span></a>
    <a href="/logout.php" class="btn btn-sm btn-secondary ms-2"><i class="fa-solid fa-right-from-bracket me-1"></i><span data-i18n="logout">Logout</span></a>
  </div>
<?php endif; ?>

<!-- ✅ Load order matters -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sweetalert.min.js"></script>

<!-- ✅ hospital app scripts -->
<script src="/assets/js/i18n.js"></script>
<script src="/assets/js/app.js"></script> 
