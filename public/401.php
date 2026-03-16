<?php
http_response_code(401);
include __DIR__ . '/../templates/header.php';
?>
<div class="container text-center mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-1" data-i18n="error_401_title">401</h1>
      <p class="lead" data-i18n="error_401_message">Unauthorized access.</p>
      <a href="/login.php" class="btn btn-primary" data-i18n="go_login">Go to login</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
