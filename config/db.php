<?php
// Simple PDO connector. Reads .env in project root if present.
$root = dirname(__DIR__);
$envFile = $root . '/.env';
$config = [
    'DB_HOST' => getenv('DB_HOST') ?: '192.168.1.204',
    'DB_NAME' => getenv('DB_NAME') ?: 'hospital',
    'DB_USER' => getenv('DB_USER') ?: 'Marianorori',
    'DB_PORT' => getenv('DB_PORT') ?: '3307',
    'DB_PASS' => getenv('DB_PASS') ?: 'SuperNoror!26*',
];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2) + [1 => '']);
        if ($k !== '') $config[$k] = $v;
    }
}

$dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production you would hide details
    die('DB Connection failed: ' . $e->getMessage());
}
