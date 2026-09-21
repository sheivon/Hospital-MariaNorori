<div class="error-page d-flex align-items-center justify-content-center vh-100">
  <div class="text-center">
    <h1 class="display-1 fw-bold"><?= htmlspecialchars((string)($errorData['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
    <h2 class="h4 mb-3"><?= htmlspecialchars((string)($errorData['heading'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
    <p class="text-muted mb-4"><?= htmlspecialchars((string)($errorData['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
    <a href="<?= htmlspecialchars((string)($errorData['buttonLink'] ?? '/'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">
      <?= htmlspecialchars((string)($errorData['buttonText'] ?? 'Go home'), ENT_QUOTES, 'UTF-8') ?>
    </a>
  </div>
</div>