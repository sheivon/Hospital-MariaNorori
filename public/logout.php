<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
Auth::logout();
// redirect to login at web root
header('Location: /login.php');
exit;
