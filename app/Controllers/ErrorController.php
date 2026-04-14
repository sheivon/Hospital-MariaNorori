<?php

namespace App\Controllers;

class ErrorController
{
    public static function render(int $code): void
    {
        $validCodes = [401, 404, 500];
        if (!in_array($code, $validCodes, true)) {
            $code = 404;
        }

        http_response_code($code);
        include __DIR__ . '/../../public/error-template.php';
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
