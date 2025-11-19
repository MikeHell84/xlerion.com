<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php'; require_once __DIR__ . '/../../src/Security.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /admin/dashboard.php'); exit; }
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); echo 'CSRF'; exit; }
$id = $_POST['id'] ?? null; $title = trim($_POST['title'] ?? ''); $slug = trim($_POST['slug'] ?? ''); $excerpt = trim($_POST['excerpt'] ?? ''); $content = $_POST['content'] ?? '';
$clean = Security::sanitizeHtml($content);
$pdo = Database::pdo();
// media placement inputs
$media_placement = trim($_POST['media_placement'] ?? 'none');
$media_url = trim($_POST['media_url'] ?? '');
if ($id) {
  $st = $pdo->prepare('UPDATE cms_pages SET title=?,slug=?,excerpt=?,content=?,updated_at=? WHERE id=?');
  $st->execute([$title,$slug,$excerpt,$clean,date('Y-m-d H:i:s'),$id]);
  // merge meta
  $s2 = $pdo->prepare('SELECT meta FROM cms_pages WHERE id = ? LIMIT 1'); $s2->execute([$id]); $r = $s2->fetch(PDO::FETCH_ASSOC);
  $meta = [];
  if ($r && !empty($r['meta'])) { $tmp = json_decode($r['meta'], true); if (is_array($tmp)) $meta = $tmp; }
  $meta['media_placement'] = $media_placement;
  $meta['media_url'] = $media_url;
  $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?'); $u->execute([json_encode($meta), $id]);
} else {
  $st = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,is_published,created_at,updated_at) VALUES (?,?,?,?,1,?,?)');
  $now = date('Y-m-d H:i:s'); $st->execute([$slug,$title,$excerpt,$clean,$now,$now]);
  $newId = $pdo->lastInsertId();
  $meta = [];
  if ($media_placement !== '' || $media_url !== '') { $meta['media_placement'] = $media_placement; $meta['media_url'] = $media_url; }
  if (!empty($meta)) { $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?'); $u->execute([json_encode($meta), $newId]); }
}
header('Location: /admin/dashboard.php'); exit;
