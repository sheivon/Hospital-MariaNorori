<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../config/db.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    if ($username === '' || $password === '') $err = 'reg_user_pass_required';
    else {
        // check username/cedula uniqueness
        $q = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
        $q->execute([':u'=>$username]);
        if ($q->fetch()) { $err = 'username_taken'; }
        else {
            if ($cedula !== ''){
                $q2 = $pdo->prepare('SELECT id FROM users WHERE cedula = :c LIMIT 1');
                $q2->execute([':c'=>$cedula]);
                if ($q2->fetch()) { $err = 'cedula_in_use'; }
            }
        }
        if ($err === ''){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username,password,fullname,cedula,role) VALUES (:u,:p,:f,:c,:r)');
            $stmt->execute([':u'=>$username,':p'=>$hash,':f'=>$fullname,':c'=>$cedula,':r'=>'user']);
            // auto-login
            if (login($username, $password)){
                header('Location: /'); exit;
            } else {
                $err = 'auto_login_failed';
            }
        }
    }
}
include __DIR__ . '/../templates/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8">
        <h3 data-i18n="register_title">Register</h3>
        <?php if ($err): ?><div class="alert alert-danger" data-i18n="<?= htmlspecialchars($err) ?>">Error</div><?php endif; ?>
        <form method="post">
            <div class="mb-3"><label class="form-label" data-i18n="label_username">Username</label><input name="username" class="form-control" required data-i18n="label_username" data-i18n-placeholder="label_username"></div>
            <div class="mb-3"><label class="form-label" data-i18n="label_password">Password</label><input name="password" type="password" class="form-control" required data-i18n="label_password" data-i18n-placeholder="label_password"></div>
            <div class="mb-3"><label class="form-label" data-i18n="label_fullname">Full name</label><input name="fullname" class="form-control" data-i18n="label_fullname" data-i18n-placeholder="label_fullname"></div>
            <div class="mb-3"><label class="form-label" data-i18n="label_cedula">Cédula</label><input name="cedula" class="form-control" data-i18n="label_cedula" data-i18n-placeholder="label_cedula"></div>
            <button class="btn btn-primary">
                <i class="fa-solid fa-user-plus me-1"></i>
                <span data-i18n="btn_register">Register</span>
            </button>
        </form>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
