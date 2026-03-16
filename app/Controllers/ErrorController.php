<?php

namespace App\Controllers;

class ErrorController
{
    public static function render(int $code): void
    {
        http_response_code($code);

        switch ($code) {
            case 401:
                include __DIR__ . '/../../public/401.php';
                break;
            case 500:
                include __DIR__ . '/../../public/500.php';
                break;
            case 404:
            default:
                include __DIR__ . '/../../public/404.php';
                break;
        }
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
