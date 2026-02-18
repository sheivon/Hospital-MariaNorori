<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hospital;charset=utf8mb4', 'root', 'Kilabone15*');
    echo 'Connected to MySQL database successfully.';
    echo ' Server version: ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>