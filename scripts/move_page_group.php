<?php
// move_page_group.php
// Usage: php move_page_group.php <slug> <new_group>
// Example: php move_page_group.php documentacion "Herramientas"
if (php_sapi_name() !== 'cli') {
  echo "This script must be run from the command line.\n";
  exit(1);
}
$slug = $argv[1] ?? null;
$newGroup = $argv[2] ?? null;
if (!$slug || !$newGroup) {
  echo "Usage: php move_page_group.php <slug> <new_group>\n";
  exit(1);
}
$db = __DIR__ . '/../storage/database.sqlite';
if (!file_exists($db)) { echo "ERROR: DB not found at $db\n"; exit(1); }
try {
  $pdo = new PDO('sqlite:' . $db);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare('SELECT id, meta FROM cms_pages WHERE slug = :slug LIMIT 1');
  $stmt->execute([':slug' => $slug]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) { echo "ERROR: slug not found: $slug\n"; exit(1); }
  $meta = [];
  if (!empty($row['meta'])) { $tmp = json_decode($row['meta'], true); if (is_array($tmp)) $meta = $tmp; }
  $meta['menu_group'] = $newGroup;
  $newMeta = json_encode($meta, JSON_UNESCAPED_UNICODE);
  $upd = $pdo->prepare('UPDATE cms_pages SET meta = :meta WHERE id = :id');
  $upd->execute([':meta' => $newMeta, ':id' => $row['id']]);
  echo "OK: updated slug {$slug} to group {$newGroup}\n";
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
  exit(1);
}
