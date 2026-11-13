<?php
require_once __DIR__ . '/../includes/bootstrap.php';
// Dang xuat trang quan tri.
unset($_SESSION['admin_logged_in']);
session_regenerate_id(true);
flash('Bạn đã đăng xuất khỏi trang quản trị.');
redirect('login.php');
