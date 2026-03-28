<?php
require_once __DIR__ . '/../src/auth.php';
require_login();
require_once __DIR__ . '/../config/db.php';
$user = current_user();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $password = $_POST['password'] ?? '';
    // check cedula uniqueness
  if ($cedula !== ''){
        $q = $pdo->prepare('SELECT id FROM users WHERE cedula = :c AND id != :id LIMIT 1');
        $q->execute([':c'=>$cedula, ':id'=>$user['id']]);
    if ($q->fetch()) $err = 'cedula_in_use';
    }
    if ($err === ''){
        if ($password !== ''){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET fullname = :f, cedula = :c, password = :p WHERE id = :id');
            $stmt->execute([':f'=>$fullname, ':c'=>$cedula, ':p'=>$hash, ':id'=>$user['id']]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET fullname = :f, cedula = :c WHERE id = :id');
            $stmt->execute([':f'=>$fullname, ':c'=>$cedula, ':id'=>$user['id']]);
        }
  // refresh session fullname
  if ($fullname) $_SESSION['user']['fullname'] = $fullname;
        // reload user data
        header('Location: /profile.php'); exit;
    }
}
// load current DB values
$stmt = $pdo->prepare('SELECT id, username, fullname, cedula FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id'=> $user['id']]);
$dbu = $stmt->fetch();
include __DIR__ . '/../templates/header.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="profile-card p-4">
        <div class="row g-4 align-items-center">
          <div class="col-md-4 text-center">
            <div class="profile-avatar mb-3" id="profileAvatar" aria-hidden="true">?</div>
            <h5 id="profileName" class="h4 mb-1"><?= htmlspecialchars($dbu['fullname'] ?: $dbu['username']) ?></h5>
            <p class="text-muted" data-i18n="profile_welcome">Profile settings</p>
          </div>
          <div class="col-md-8">
            <div class="profile-section p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" data-i18n="profile_title">Profile</h5>
                <span class="badge bg-info text-dark" data-i18n="profile_role">Role: </span>
              </div>
              <p class="text-secondary mt-2"><?= htmlspecialchars($user['role'] ?? 'user') ?></p>
            </div>
            <?php if ($err): ?>
              <div class="alert alert-danger" data-i18n="<?=htmlspecialchars($err)?>"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>
            <form method="post" class="row g-3">
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_username">Username</label>
                <input class="form-control" value="<?=htmlspecialchars($dbu['username'])?>" disabled>
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_cedula">Cédula</label>
                <input name="cedula" class="form-control" value="<?=htmlspecialchars($dbu['cedula']??'')?>" placeholder="<?= htmlspecialchars($dbu['cedula']??'') ?>">
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_fullname">Full name</label>
                <input name="fullname" class="form-control" value="<?=htmlspecialchars($dbu['fullname']??'')?>" placeholder="<?= htmlspecialchars($dbu['fullname']??'') ?>">
              </div>
              <div class="col-sm-6">
                <label class="form-label profile-label" data-i18n="label_new_password">New password (leave empty to keep)</label>
                <input name="password" type="password" class="form-control" placeholder="********">
              </div>
              <div class="col-12 text-end">
                <button class="btn btn-primary btn-acrylic">
                  <i class="fa-solid fa-floppy-disk me-1"></i>
                  <span data-i18n="btn_save">Save</span>
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
  const name = <?= json_encode($dbu['fullname'] ?: $dbu['username']) ?>;
  const initials = name.trim().split(/\s+/).slice(0,2).map(w=>w[0]?.toUpperCase()).join('') || '?';
  const avatar = document.getElementById('profileAvatar');
  if (avatar) avatar.textContent = initials;
})();
</script>

<?php include __DIR__ . '/../templates/footer.php';
