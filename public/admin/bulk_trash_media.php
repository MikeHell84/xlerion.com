<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

$ids = $_POST['files'] ?? [];
if (!is_array($ids) || empty($ids)) { header('Location: /admin/media_manager.php'); exit; }

$pdo = Database::pdo();
try {
  $pdo->beginTransaction();
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT id FROM media_files WHERE id IN ($placeholders)");
  $stmt->execute($ids); $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $now = date('c'); $user = $_SESSION['user_id'] ?? null;
  $upd = $pdo->prepare("UPDATE media_files SET deleted_at = ?, deleted_by = ? WHERE id = ?");
  foreach ($rows as $r) { $upd->execute([$now, $user, $r['id']]); }
  $pdo->commit();
  $_SESSION['flash'] = count($rows) . " archivos movidos a papelera.";
} catch (Exception $e) {
  $pdo->rollBack();
  $_SESSION['flash'] = "Error: " . $e->getMessage();
}
header('Location: /admin/media_manager.php'); exit;
