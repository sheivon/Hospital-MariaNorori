<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\AuthHelper;

AuthHelper::bootSession();

function login($username, $password) {
    return AuthHelper::login((string)$username, (string)$password);
}

function require_login() {
    AuthHelper::requireLogin();
}

function current_user() {
    return AuthHelper::currentUser();
}

function logout() {
    AuthHelper::logout();
}

function require_role($role){
    AuthHelper::requireRole((string)$role);
}

function is_admin(){
    return AuthHelper::isAdmin();
}
