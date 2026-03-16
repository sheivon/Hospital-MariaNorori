<?php
http_response_code(500);
include __DIR__ . '/../templates/header.php';
?>
<div class="container text-center mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-1" data-i18n="error_500_title">500</h1>
      <p class="lead" data-i18n="error_500_message">Internal server error.</p>
      <a href="/" class="btn btn-primary" data-i18n="go_home">Go to home</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
