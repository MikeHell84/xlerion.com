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
// --- resources: parse and validate BEFORE saving ---
$postedResources = [];
if (!empty($_POST['resources_order'])) {
  $parts = array_map('trim', explode(',', $_POST['resources_order']));
  foreach ($parts as $rs) { $s = preg_replace('/[^a-z0-9\-]+/i','', $rs); if ($s==='') continue; $postedResources[] = $s; }
} elseif (!empty($_POST['resources']) && is_array($_POST['resources'])) {
  foreach ($_POST['resources'] as $rs) { $s = preg_replace('/[^a-z0-9\-]+/i','', trim($rs)); if ($s==='') continue; $postedResources[] = $s; }
}
$postedResources = array_values(array_unique($postedResources));
// if any posted resources, validate existence in DB now; block save if some missing
$invalidResources = [];
if (!empty($postedResources)) {
  try {
    $place = implode(',', array_fill(0, count($postedResources), '?'));
    $chk = $pdo->prepare('SELECT slug FROM resources WHERE slug IN (' . $place . ')');
    $chk->execute($postedResources);
    $found = $chk->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    $invalidResources = array_values(array_diff($postedResources, $found));
  } catch (Exception $e) { /* On DB error, treat as validation failure */ $invalidResources = $postedResources; }
}
// menu-related inputs
$menu_visible = isset($_POST['menu_visible']) && $_POST['menu_visible'] ? 1 : 0;
$menu_group_new = trim($_POST['menu_group_new'] ?? '');
$menu_group = trim($_POST['menu_group'] ?? '');
$menu_parent = intval($_POST['menu_parent'] ?? 0);
$menu_order = intval($_POST['menu_order'] ?? 0);
if ($id) {
  $st = $pdo->prepare('UPDATE cms_pages SET title=?,slug=?,excerpt=?,content=?,updated_at=? WHERE id=?');
  $st->execute([$title,$slug,$excerpt,$clean,date('Y-m-d H:i:s'),$id]);
  // merge meta
  $s2 = $pdo->prepare('SELECT meta FROM cms_pages WHERE id = ? LIMIT 1'); $s2->execute([$id]); $r = $s2->fetch(PDO::FETCH_ASSOC);
  $meta = [];
  if ($r && !empty($r['meta'])) { $tmp = json_decode($r['meta'], true); if (is_array($tmp)) $meta = $tmp; }
  $meta['media_placement'] = $media_placement;
  $meta['media_url'] = $media_url;
  // subtitle uppercase preference
  $meta['subtitle_uppercase'] = isset($_POST['subtitle_uppercase']) && $_POST['subtitle_uppercase'] ? 1 : 0;
  // attach validated resources (auto-remove invalid slugs)
  if (!empty($postedResources)) {
    $valid = array_values(array_diff($postedResources, $invalidResources));
    if (!empty($invalidResources)) {
      $_SESSION['flash'] = 'Se han eliminado recursos no válidos: ' . implode(', ', $invalidResources);
    }
    if (!empty($valid)) $meta['resources'] = $valid; else unset($meta['resources']);
  } else unset($meta['resources']);
  // menu meta
  $meta['menu_visible'] = $menu_visible ? 1 : 0;
  // if a new group was provided, prefer it over selected existing
  if ($menu_group_new !== '') $meta['menu_group'] = $menu_group_new; else if ($menu_group !== '') $meta['menu_group'] = $menu_group;
  if ($menu_parent) $meta['menu_parent'] = $menu_parent; else unset($meta['menu_parent']);
  if ($menu_order) $meta['menu_order'] = $menu_order; else unset($meta['menu_order']);
  // Ensure pages marked as visible without an explicit group still appear in the main menu:
  // assign to a default 'Principal' group when user checked "Mostrar en el menú principal" but left group empty.
  if ($meta['menu_visible'] && empty($meta['menu_group']) && empty($meta['menu_parent'])) {
    $meta['menu_group'] = 'Principal';
  }
  $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?'); $u->execute([json_encode($meta), $id]);
  // persist template assignments (header/footer)
  try{
    $hdr = intval($_POST['header_template_id'] ?? 0) ?: null;
    $ftr = intval($_POST['footer_template_id'] ?? 0) ?: null;
    // header
    $del = $pdo->prepare('DELETE FROM template_assignments WHERE page_id = ? AND section = ?');
    if ($hdr){ $del->execute([$id,'header']); $ins = $pdo->prepare('INSERT OR REPLACE INTO template_assignments (page_id,section,template_id,updated_at) VALUES (?,?,?,?)'); $ins->execute([$id,'header',$hdr,date('Y-m-d H:i:s')]); } else { $del->execute([$id,'header']); }
    // footer
    if ($ftr){ $del->execute([$id,'footer']); $ins->execute([$id,'footer',$ftr,date('Y-m-d H:i:s')]); } else { $del->execute([$id,'footer']); }
  }catch(Exception $e){ /* ignore assignment errors for now */ }
} else {
  $st = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,is_published,created_at,updated_at) VALUES (?,?,?,?,1,?,?)');
  $now = date('Y-m-d H:i:s'); $st->execute([$slug,$title,$excerpt,$clean,$now,$now]);
  $newId = $pdo->lastInsertId();
  $meta = [];
  if ($media_placement !== '' || $media_url !== '') { $meta['media_placement'] = $media_placement; $meta['media_url'] = $media_url; }
  // attach validated resources for newly created page as well (auto-remove invalids)
  if (!empty($postedResources)) {
    $valid = array_values(array_diff($postedResources, $invalidResources));
    if (!empty($invalidResources)) {
      $_SESSION['flash'] = 'Se han eliminado recursos no válidos: ' . implode(', ', $invalidResources);
    }
    if (!empty($valid)) $meta['resources'] = $valid;
  }
  if (!empty($meta)) { $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?'); $u->execute([json_encode($meta), $newId]); }

  // If user checked menu_visible for a new page but didn't provide grouping, ensure it appears in menu
  // Always ensure subtitle_uppercase is stored for new pages (default true if not specified)
  $tmpMeta = $meta;
  $tmpMeta['subtitle_uppercase'] = isset($_POST['subtitle_uppercase']) ? (int)($_POST['subtitle_uppercase'] ? 1 : 0) : 1;
  if ($menu_group_new !== '') $tmpMeta['menu_group'] = $menu_group_new; else if ($menu_group !== '') $tmpMeta['menu_group'] = $menu_group;
  if ($menu_visible) {
    $tmpMeta['menu_visible'] = 1;
    if (empty($tmpMeta['menu_group']) && empty($menu_parent)) { $tmpMeta['menu_group'] = 'Principal'; }
  }
  // Persist merged meta back to page record
  $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?'); $u->execute([json_encode($tmpMeta), $newId]);
  // persist template assignments (header/footer) for new page
  try{
    $hdr = intval($_POST['header_template_id'] ?? 0) ?: null;
    $ftr = intval($_POST['footer_template_id'] ?? 0) ?: null;
    if ($hdr){ $ins = $pdo->prepare('INSERT OR REPLACE INTO template_assignments (page_id,section,template_id,updated_at) VALUES (?,?,?,?)'); $ins->execute([$newId,'header',$hdr,date('Y-m-d H:i:s')]); }
    if ($ftr){ $ins->execute([$newId,'footer',$ftr,date('Y-m-d H:i:s')]); }
  }catch(Exception $e){}
}
// Return to pages listing after successful save
header('Location: /admin/pages.php'); exit;
