<?php
$db = __DIR__ . '/../storage/database.sqlite';
if (!file_exists($db)) { echo "DB missing: $db\n"; exit(2); }
$pdo = new PDO('sqlite:' . $db);
$st = $pdo->query("SELECT slug,title,is_published FROM cms_pages ORDER BY id");
if (!$st) { echo "Query failed\n"; exit(3); }
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
  echo ($r['is_published'] ? 'P' : 'D') . "\t" . ($r['slug'] ?? '') . "\t" . ($r['title'] ?? '') . PHP_EOL;
}
