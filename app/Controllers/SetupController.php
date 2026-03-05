<?php

namespace App\Controllers;

use App\Models\SetupModel;
use Throwable;

class SetupController
{
    public static function handle(array $post): string
    {
        $setup = new SetupModel();
        $config = $setup->loadConfig();
        $schemaFile = APP_ROOT . '/migrations/init.sql';

        $messages = [];

        try {
            if (isset($post['create_db'])) {
                $messages[] = $setup->createDatabase($config);
            } elseif (isset($post['create_tables'])) {
                $messages[] = $setup->runSchemaFromFile($config, $schemaFile);
            } elseif (isset($post['create_users']) || isset($post['create_admin'])) {
                $messages = array_merge($messages, $setup->createDefaultUsers($config));
            } elseif (isset($post['drop_tables'])) {
                $messages = array_merge($messages, $setup->dropAllTables($config));
            } elseif (isset($post['initialize_db'])) {
                $messages[] = $setup->createDatabase($config);
                $messages[] = $setup->runSchemaFromFile($config, $schemaFile);
                $messages = array_merge($messages, $setup->createDefaultUsers($config));
                $messages[] = 'Database initialization completed.';
            }
        } catch (Throwable $e) {
            $messages[] = 'Error: ' . $e->getMessage();
        }

        return implode(PHP_EOL, $messages);
    }
}
