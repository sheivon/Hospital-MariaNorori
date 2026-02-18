<?php
session_start();
require_once __DIR__ . '/../config/db.php';

function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u'=>$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        // store minimal info in session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'] ?? $user['username'],
            'role' => $user['role'] ?? 'user'
        ];
        return true;
    }
    return false;
}

function require_login() {
    if (empty($_SESSION['user'])) {
        // If this is an API call, return JSON 401 so AJAX callers can handle it
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        // public/ is the document root, so link to /login.php (not /public/login.php)
        header('Location: /login.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function logout() {
    session_unset();
    session_destroy();
}

// Role helpers
function require_role($role){
    require_login();
    $u = current_user();
    if (!$u || strtolower($u['role'] ?? 'user') !== strtolower($role)){
        http_response_code(403);
        if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') === 0){
            header('Content-Type: application/json');
            echo json_encode(['success'=>false,'error'=>'Forbidden']);
        } else {
            echo '<div style="padding:2rem; font-family: system-ui, sans-serif;">Access denied</div>';
        }
        exit;
    }
}

function is_admin(){
    $u = current_user();
    return $u && strtolower($u['role'] ?? 'user') === 'admin';
}
