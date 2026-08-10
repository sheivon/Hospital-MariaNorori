<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Repositories\TableCrudRepository;

try {
    $repo = new TableCrudRepository();
    echo "[vitals listRows]\n";
    var_dump($repo->listRows('vitals', 5));
} catch (Throwable $e) {
    echo "EXCEPTION: " . get_class($e) . "\n";
    echo "MESSAGE: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
