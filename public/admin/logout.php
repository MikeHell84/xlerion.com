<?php
require_once __DIR__ . '/../../src/Auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::logout();
header('Location: /admin/login.php'); exit;
