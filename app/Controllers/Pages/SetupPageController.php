<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Controllers\SetupController;
use App\Repositories\SetupRepository;

class SetupPageController
{
    /**
     * @param array<string,mixed> $post
     * @return array{message:string, posted:array<string,mixed>, mysqlExtensionsLoaded:bool}
     */
    public static function viewModel(array $post, string $method): array
    {
        $config = (new SetupRepository())->loadConfig();

        return [
            'message' => $method === 'POST' ? SetupController::handle($post) : '',
            'posted' => array_merge($config, $post),
            'mysqlExtensionsLoaded' => extension_loaded('pdo_mysql') || extension_loaded('mysqli'),
        ];
    }
}
