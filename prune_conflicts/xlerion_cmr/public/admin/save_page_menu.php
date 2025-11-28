<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /admin/dashboard.php'); exit; }
$pdo = Database::pdo();
$id = intval($_POST['id'] ?? 0);
$menu_group = trim($_POST['menu_group'] ?? '');
$menu_parent = trim($_POST['menu_parent'] ?? '');
try {
  $stmt = $pdo->prepare('SELECT meta FROM cms_pages WHERE id = ? LIMIT 1');
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $meta = [];
  if ($row && !empty($row['meta'])) {
    $m = json_decode($row['meta'], true);
    if (is_array($m)) $meta = $m;
  }
  // Validation: parent cannot equal self
  if ($menu_parent !== '' && intval($menu_parent) === $id) {
    $_SESSION['flash_error'] = 'La página no puede ser su propio padre.';
    header('Location: /admin/dashboard.php'); exit;
  }
  // If parent provided, ensure it exists
  if ($menu_parent !== '') {
    $pstmt = $pdo->prepare('SELECT id, meta FROM cms_pages WHERE id = ? LIMIT 1');
    $pstmt->execute([intval($menu_parent)]);
    $prow = $pstmt->fetch(PDO::FETCH_ASSOC);
    if (!$prow) {
      $_SESSION['flash_error'] = 'El padre seleccionado no existe.';
      header('Location: /admin/dashboard.php'); exit;
    }
  }
  // Cycle detection: walk up parents from candidate parent and ensure we don't hit the child
  if ($menu_parent !== '') {
    $seen = [];
    $cur = intval($menu_parent);
    while ($cur) {
      if (isset($seen[$cur])) break;
      $seen[$cur] = true;
      if ($cur === $id) {
        $_SESSION['flash_error'] = 'Asignación inválida: crearía un ciclo en la jerarquía.';
        header('Location: /admin/dashboard.php'); exit;
      }
      $s = $pdo->prepare('SELECT meta FROM cms_pages WHERE id = ? LIMIT 1');
      $s->execute([$cur]);
      $r = $s->fetch(PDO::FETCH_ASSOC);
      $metaCur = [];
      if ($r && !empty($r['meta'])) { $mm = json_decode($r['meta'], true); if (is_array($mm)) $metaCur = $mm; }
      $cur = isset($metaCur['menu_parent']) ? intval($metaCur['menu_parent']) : 0;
    }
  }
  if ($menu_group === '') { unset($meta['menu_group']); } else { $meta['menu_group'] = $menu_group; }
  if ($menu_parent === '') { unset($meta['menu_parent']); } else { $meta['menu_parent'] = $menu_parent; }
  $metaJson = json_encode($meta);
  $up = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?');
  $up->execute([$metaJson, $id]);
  $_SESSION['flash'] = 'Asignación de menú guardada.';
} catch (Exception $e) {
  $_SESSION['flash_error'] = 'Error al guardar la asignación: ' . $e->getMessage();
}
header('Location: /admin/dashboard.php'); exit;
