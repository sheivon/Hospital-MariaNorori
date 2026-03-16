<?php
http_response_code(404);
include __DIR__ . '/../templates/header.php';
?>
<div class="container text-center mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-1" data-i18n="error_404_title">404</h1>
      <p class="lead" data-i18n="error_404_message">Page not found.</p>
      <a href="/" class="btn btn-primary" data-i18n="go_home">Go to home</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
