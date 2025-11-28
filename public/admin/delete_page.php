<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /admin/dashboard.php'); exit; }
$id = intval($_POST['id'] ?? 0);
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); echo 'CSRF'; exit; }
if ($id>0) {
  $pdo = Database::pdo();
  $pdo->prepare('DELETE FROM cms_pages WHERE id = ?')->execute([$id]);
}
// Prefer explicit return_to (from form), then safe HTTP_REFERER, else dashboard
$return = $_POST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
if ($return) {
  $u = parse_url($return);
  if (isset($u['path']) && strpos($u['path'], '/admin') === 0) { header('Location: ' . $return); exit; }
}
header('Location: /admin/dashboard.php'); exit;
