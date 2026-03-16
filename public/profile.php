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
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3 data-i18n="profile_title">Profile</h3>
    <?php if ($err): ?><div class="alert alert-danger" data-i18n="<?=htmlspecialchars($err)?>">Error</div><?php endif; ?>
    <form method="post">
      <div class="mb-3"><label class="form-label" data-i18n="label_username">Username</label><input class="form-control" value="<?=htmlspecialchars($dbu['username'])?>" disabled></div>
      <div class="mb-3"><label class="form-label" data-i18n="label_fullname">Full name</label><input name="fullname" class="form-control" value="<?=htmlspecialchars($dbu['fullname']??'')?>" data-i18n="label_fullname" data-i18n-placeholder="label_fullname"></div>
      <div class="mb-3"><label class="form-label" data-i18n="label_cedula">Cédula</label><input name="cedula" class="form-control" value="<?=htmlspecialchars($dbu['cedula']??'')?>" data-i18n="label_cedula" data-i18n-placeholder="label_cedula"></div>
      <div class="mb-3"><label class="form-label" data-i18n="label_new_password">New password (leave empty to keep)</label><input name="password" type="password" class="form-control" data-i18n="label_new_password" data-i18n-placeholder="label_new_password"></div>
      <button class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk me-1"></i>
        <span data-i18n="btn_save">Save</span>
      </button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
