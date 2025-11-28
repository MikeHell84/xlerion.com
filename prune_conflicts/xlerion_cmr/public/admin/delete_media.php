<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['file'])) { header('Location: /admin/media_manager.php'); exit; }
$name = basename($_POST['file']);
try {
  $pdo = Database::pdo();
  $st = $pdo->prepare('SELECT id FROM media_files WHERE filename = ? LIMIT 1'); $st->execute([$name]); $mid = $st->fetchColumn();
  if ($mid) {
    $now = date('c'); $user = $_SESSION['user_id'] ?? null;
    $pdo->prepare('UPDATE media_files SET deleted_at=?, deleted_by=? WHERE id=?')->execute([$now,$user,$mid]);
    $_SESSION['flash'] = 'Archivo movido a papelera.';
  }
} catch (Exception $e) { /* ignore DB errors */ }
header('Location: /admin/media_manager.php'); exit;

