<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$ids = $_POST['files'] ?? []; if (!is_array($ids)||empty($ids)) { header('Location:/admin/media_manager.php'); exit; }
$pdo = Database::pdo();
try {
  $pdo->beginTransaction();
  $placeholders = implode(',', array_fill(0,count($ids),'?'));
  $stmt = $pdo->prepare("SELECT id, url, thumb320, thumb720, filename FROM media_files WHERE id IN ($placeholders)");
  $stmt->execute($ids); $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $r) {
    $paths = [];
    if (!empty($r['url'])) $paths[] = __DIR__ . '/..' . $r['url'];
    if (!empty($r['thumb320'])) $paths[] = __DIR__ . '/..' . $r['thumb320'];
    if (!empty($r['thumb720'])) $paths[] = __DIR__ . '/..' . $r['thumb720'];
    foreach ($paths as $p) if (file_exists($p)) @unlink($p);
  }
  $delJobs = $pdo->prepare("DELETE FROM media_jobs WHERE media_id IN ($placeholders)"); $delJobs->execute($ids);
  $del = $pdo->prepare("DELETE FROM media_files WHERE id IN ($placeholders)"); $del->execute($ids);
  $pdo->commit(); $_SESSION['flash'] = count($rows) . ' archivos eliminados permanentemente.';
} catch (Exception $e) { $pdo->rollBack(); $_SESSION['flash'] = 'Error: '.$e->getMessage(); }
header('Location: /admin/media_manager.php'); exit;
