<?php
// Lightweight debug endpoint to inspect session cookie and Auth state.
// Safe to keep temporarily; remove when debugging is finished.
require_once __DIR__ . '/../../src/Auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json');

$out = [
  'now' => date('c'),
  'request_cookie_header' => $_SERVER['HTTP_COOKIE'] ?? null,
  'session_name' => session_name(),
  'session_id' => session_id(),
  'session_contents' => $_SESSION ?? null,
  'auth_check' => Auth::check(),
  'auth_user' => Auth::user(),
];

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
