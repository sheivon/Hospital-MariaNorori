<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::bootSession();

function login($username, $password) {
    return Auth::login((string)$username, (string)$password);
}

function require_login() {
    Auth::requireLogin();
}

function current_user() {
    return Auth::currentUser();
}

function logout() {
    Auth::logout();
}

function require_role($role){
    Auth::requireRole((string)$role);
}

function is_admin(){
    return Auth::isAdmin();
}
