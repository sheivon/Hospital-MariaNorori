<?php
$errorCode = $code;
$errorInfo = [
    401 => [
        'title' => '401',
        'heading' => 'No autorizado',
        'message' => 'No tiene permiso para acceder a esta página.',
        'buttonText' => 'Ir a inicio de sesión',
        'buttonLink' => '/login.php',
    ],
    404 => [
        'title' => '404',
        'heading' => 'Página no encontrada',
        'message' => 'La página solicitada no existe o fue movida.',
        'buttonText' => 'Ir al inicio',
        'buttonLink' => '/',
    ],
    500 => [
        'title' => '500',
        'heading' => 'Error interno del servidor',
        'message' => 'Se produjo un error en el servidor. Intente nuevamente más tarde.',
        'buttonText' => 'Ir al inicio',
        'buttonLink' => '/',
    ],
];

if (!isset($errorInfo[$errorCode])) {
    $errorCode = 404;
}

$errorData = $errorInfo[$errorCode];

include __DIR__ . '/../templates/header.php';
?>
<div class="container text-center mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-1"><?php echo htmlspecialchars($errorData['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <h2 class="mb-3"><?php echo htmlspecialchars($errorData['heading'], ENT_QUOTES, 'UTF-8'); ?></h2>
      <p class="lead"><?php echo htmlspecialchars($errorData['message'], ENT_QUOTES, 'UTF-8'); ?></p>
      <a href="<?php echo htmlspecialchars($errorData['buttonLink'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
        <?php echo htmlspecialchars($errorData['buttonText'], ENT_QUOTES, 'UTF-8'); ?>
      </a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../templates/footer.php';
