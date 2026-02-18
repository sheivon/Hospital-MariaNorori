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
  <div class="auth-screen d-flex align-items-center justify-content-center navbar-custom" style="min-height: calc(100vh - 170px);">
    <div class="card card-glass shadow-lg" style="width:420px;max-width:92vw; margin: 0 12px;">
      <div class="card-body p-4">
        <div class="text-center mb-3">
          <i class="fa-solid fa-user-lock fa-2x text-primary"></i>
          <h3 class="mt-2 mb-0" data-i18n="login_title">Login</h3>
        </div>
        <?php if (!empty($err)): ?>
          <div class="alert alert-danger" data-i18n="<?= htmlspecialchars($err) ?>">Invalid username or password</div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label" data-i18n="label_username">Username</label>
            <input name="username" class="form-control" required placeholder="Username" data-i18n="label_username">
          </div>
          <div class="mb-3">
            <label class="form-label" data-i18n="label_password">Password</label>
            <input name="password" type="password" class="form-control" required placeholder="Password" data-i18n="label_password">
          </div>
          <button class="btn btn-primary w-100"><i class="fa-solid fa-right-to-bracket me-1"></i><span data-i18n="btn_login">Login</span></button>
        </form>
      </div>
    </div>
  </div>
  <script>
    // Adjust the auth-screen min-height to account for the navbar height dynamically
    document.addEventListener('DOMContentLoaded', function(){
      try{
        var nav = document.querySelector('nav.navbar');
        var auth = document.querySelector('.auth-screen');
        if (nav && auth) {
          var navHeight = nav.getBoundingClientRect().height || 0;
          // set minHeight to viewport minus navbar height, minus a tiny buffer
          auth.style.minHeight = 'calc(100vh - ' + Math.ceil(navHeight) + 'px)';
        }
      }catch(e){ console.warn('auth-screen resize failed', e); }
    });
  </script>
<?php else: ?>
  <div class="alert alert-info text-center mt-3">
    <p class="" data-i18n="already_logged_in">You are already logged in as </p>
    <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>.
    <a href="/patients.php" class="btn btn-sm btn-primary ms-2" data-i18n="go_patients">Go to Patients Dashboard</a>
    <a href="/logout.php" class="btn btn-sm btn-secondary ms-2" data-i18n="logout">Logout</a>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../templates/footer.php';
