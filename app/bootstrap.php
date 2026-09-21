<?php

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = APP_ROOT . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

use App\Core\Auth;
Auth::bootSession();

if (!headers_sent() && extension_loaded('zlib')) {
    ob_start('ob_gzhandler');
}

/*
 * Friendly error pages for uncaught exceptions and PHP errors.
 * Already-running output (e.g. during a partial render) is discarded
 * so the user sees a clean error view instead of a broken page.
 */
use App\Controllers\ErrorController;

$GLOBALS['_app_handling_error'] = false;

$renderError = static function (int $code): void {
    if ($GLOBALS['_app_handling_error']) {
        return;
    }
    $GLOBALS['_app_handling_error'] = true;

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        ErrorController::render($code);
    }
};

set_exception_handler(static function (Throwable $e) use ($renderError): void {
    error_log($e);
    $renderError(500);
});

set_error_handler(static function (int $severity, string $message, string $file = '', int $line = 0) use ($renderError): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
    if (!($severity & $fatal)) {
        // Warnings/notices/deprecations are logged but do not stop the page.
        error_log(sprintf('PHP notice/warning: %s in %s:%d', $message, $file, $line));
        return false;
    }

    error_log(sprintf('PHP error: %s in %s:%d', $message, $file, $line));
    $renderError(500);
    return true;
});

register_shutdown_function(static function () use ($renderError): void {
    $error = error_get_last();
    if ($error === null) {
        return;
    }
    $fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
    if (!($error['type'] & $fatal)) {
        return;
    }
    error_log(sprintf('Fatal PHP error: %s in %s:%d', $error['message'], $error['file'], $error['line']));
    $renderError(500);
});
