<?php
/**
 * Entry point + PHP built-in server router fallback.
 *
 * When served with `php -S`, this file is invoked for any request that does
 * not match a real file/directory under public/. We route existing files
 * directly and forward everything else (including 404s) to the friendly
 * error page or, for the root path, to the dashboard controller.
 */

if (PHP_SAPI === 'cli-server') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $uri;

    // Let the built-in server return real files (assets, other .php pages, etc.)
    if ($uri !== '/' && is_file($file)) {
        return false;
    }

    // A request for a directory or a non-existent PHP page should show the
    // friendly error view instead of the default PHP 404 page.
    if ($uri !== '/' && $uri !== '/index.php') {
        $_GET['code'] = 404;
        require __DIR__ . '/error.php';
        return;
    }
}

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\Pages\DashboardPageController;

DashboardPageController::index();
