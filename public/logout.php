<?php
require_once __DIR__ . '/../src/auth.php';
logout();
// redirect to login at web root
header('Location: /login.php');
exit;
