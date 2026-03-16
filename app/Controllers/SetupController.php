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

        // Allow overriding config via form inputs
        $overrideMap = [
            'DB_HOST' => 'db_host',
            'DB_PORT' => 'db_port',
            'DB_NAME' => 'db_name',
            'DB_USER' => 'db_user',
            'DB_PASS' => 'db_pass',
        ];
        foreach ($overrideMap as $key => $field) {
            if (array_key_exists($field, $post)) {
                $config[$key] = trim((string)$post[$field]);
            }
        }

        $messages = [];

        try {
            if (isset($post['test_connection'])) {
                $messages[] = $setup->testConnection($config);
            } elseif (isset($post['save_config'])) {
                $messages[] = $setup->saveConfig($config);
            } elseif (isset($post['create_db'])) {
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
            $messages[] = sprintf(
                'DB config in use -> host: %s, port: %s, db: %s, user: %s',
                $config['DB_HOST'] ?? '',
                $config['DB_PORT'] ?? '',
                $config['DB_NAME'] ?? '',
                $config['DB_USER'] ?? ''
            );
        }

        return implode(PHP_EOL, $messages);
    }
}
