<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

$ids = $_POST['files'] ?? [];
if (!is_array($ids) || empty($ids)) {
  header('Location: /admin/media_manager.php');
  exit;
}

$pdo = Database::pdo();
try {
  $pdo->beginTransaction();
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT * FROM media_files WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // delete files
  foreach ($rows as $r) {
    $paths = [];
    if (!empty($r['url'])) $paths[] = __DIR__ . '/..' . $r['url'];
    if (!empty($r['thumb320'])) $paths[] = __DIR__ . '/..' . $r['thumb320'];
    if (!empty($r['thumb720'])) $paths[] = __DIR__ . '/..' . $r['thumb720'];
    foreach ($paths as $p) {
      if (file_exists($p)) @unlink($p);
    }
  }

  // delete jobs
  $delJobs = $pdo->prepare("DELETE FROM media_jobs WHERE media_id IN ($placeholders)");
  $delJobs->execute($ids);

  // delete files rows
  $del = $pdo->prepare("DELETE FROM media_files WHERE id IN ($placeholders)");
  $del->execute($ids);

  $pdo->commit();
  $_SESSION['flash'] = count($rows) . " archivos eliminados.";
} catch (Exception $e) {
  $pdo->rollBack();
  $_SESSION['flash'] = "Error al eliminar: " . $e->getMessage();
}

header('Location: /admin/media_manager.php');
exit;
