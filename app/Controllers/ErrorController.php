<?php

namespace App\Controllers;

class ErrorController
{
    public static function render(int $code): void
    {
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

        if (!isset($errorInfo[$code])) {
            $code = 404;
        }

        http_response_code($code);

        // Make sure no partial output leaks before the error page renders.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $errorData = $errorInfo[$code];
        $hideSidebar = true;
        $hideLanguageSelect = true;

        include APP_ROOT . '/templates/header.php';
        include APP_ROOT . '/app/Views/Shared/loading_overlay.php';
        include APP_ROOT . '/app/Views/Pages/Error/Index.php';
        include APP_ROOT . '/templates/footer.php';
        exit;
    }

    public static function json(int $code): void
    {
        http_response_code($code);
        header('Content-Type: application/json');

        $messages = [
            401 => 'Unauthorized access',
            404 => 'Not found',
            500 => 'Internal server error',
        ];

        echo json_encode([
            'success' => false,
            'error' => $messages[$code] ?? 'Error',
            'code' => $code,
            'redirect' => sprintf('/%d.php', $code),
        ]);
        exit;
    }
}
