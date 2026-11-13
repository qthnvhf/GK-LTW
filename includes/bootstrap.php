<?php
// Khoi tao session va nap cac ham chung cho tat ca cac trang.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('QLNhanSu_Session');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}
