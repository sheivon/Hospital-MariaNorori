<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\ErrorController;

$code = isset($_GET['code']) ? (int)$_GET['code'] : 404;

// Prevent the global exception handler from catching issues inside the error page itself.
try {
    ErrorController::render($code);
} catch (Throwable $e) {
    error_log('Error page failure: ' . $e);
    http_response_code($code);
    echo '<h1>Error ', (int)$code, '</h1>';
    echo '<p>Something went wrong while rendering the error page.</p>';
}
