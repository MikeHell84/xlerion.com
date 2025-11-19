<?php
// Persist menu ordering sent from admin dashboard
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'method']);
  exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!$data || !isset($data['order'])) {
  echo json_encode(['ok'=>false,'error'=>'invalid_payload']);
  exit;
}

$db = Database::pdo();

// Flatten hierarchical order into page_id => numeric order per group and parent
function persistGroup($db, $group, $items, $parentId = null, &$counter = 0) {
  foreach ($items as $it) {
    $pageId = intval($it['id']);
    // load existing meta
    $stmt = $db->prepare('SELECT meta FROM cms_pages WHERE id = :id');
    $stmt->execute([':id'=>$pageId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $meta = [];
    if ($row && $row['meta']) {
      $meta = json_decode($row['meta'], true) ?: [];
    }
    $meta['menu_group'] = $group;
    $meta['menu_parent'] = $parentId;
    $meta['menu_order'] = ++$counter;
    $u = $db->prepare('UPDATE cms_pages SET meta = :meta WHERE id = :id');
    $u->execute([':meta'=>json_encode($meta),'id'=>$pageId]);
    if (!empty($it['children'])) {
      persistGroup($db, $group, $it['children'], $pageId, $counter);
    }
  }
}

try {
  $db->beginTransaction();
  foreach ($data['order'] as $group) {
    $counter = 0;
    persistGroup($db, $group['group'] ?? 'main', $group['items'] ?? [], null, $counter);
  }
  $db->commit();
  echo json_encode(['ok'=>true]);
} catch (Exception $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
