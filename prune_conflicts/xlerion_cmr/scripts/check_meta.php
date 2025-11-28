<?php
// check_meta.php
// Usage: php check_meta.php <slug>
if (php_sapi_name() !== 'cli') { echo "Run from CLI only\n"; exit(1); }
$slug = $argv[1] ?? null;
if (!$slug) { echo "Usage: php check_meta.php <slug>\n"; exit(1); }
$db = __DIR__ . '/../storage/database.sqlite';
if (!file_exists($db)) { echo "DB not found at $db\n"; exit(1); }
try {
  $pdo = new PDO('sqlite:' . $db);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $pdo->prepare('SELECT id, meta FROM cms_pages WHERE slug = :slug LIMIT 1');
  $stmt->execute([':slug' => $slug]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) { echo "Not found: $slug\n"; exit(1); }
  echo "id=" . $row['id'] . PHP_EOL;
  echo ($row['meta'] ?: "meta empty") . PHP_EOL;
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . PHP_EOL; exit(1);
}
