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
header('Location: /admin/dashboard.php'); exit;
