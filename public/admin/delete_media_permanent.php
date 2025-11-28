<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['file'])) { header('Location: /admin/media_manager.php'); exit; }
$name = basename($_POST['file']);
try {
  $pdo = Database::pdo();
  $st = $pdo->prepare('SELECT id, url, thumb320, thumb720 FROM media_files WHERE filename = ? LIMIT 1'); $st->execute([$name]); $r = $st->fetch(PDO::FETCH_ASSOC);
  if ($r) {
    $paths = [];
    if (!empty($r['url'])) $paths[] = __DIR__ . '/..' . $r['url'];
    if (!empty($r['thumb320'])) $paths[] = __DIR__ . '/..' . $r['thumb320'];
    if (!empty($r['thumb720'])) $paths[] = __DIR__ . '/..' . $r['thumb720'];
    foreach ($paths as $p) if (file_exists($p)) @unlink($p);
    $pdo->prepare('DELETE FROM media_jobs WHERE media_id = ?')->execute([$r['id']]);
    $pdo->prepare('DELETE FROM media_files WHERE id = ?')->execute([$r['id']]);
    $_SESSION['flash'] = 'Archivo eliminado permanentemente.';
  }
} catch (Exception $e) { $_SESSION['flash'] = 'Error: '.$e->getMessage(); }
header('Location: /admin/media_manager.php'); exit;
