<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$st = $pdo->prepare('SELECT content FROM cms_pages WHERE slug = ? LIMIT 1');
$st->execute(['blog']);
$r = $st->fetch(PDO::FETCH_ASSOC);
if (!$r) { echo "No row for slug=blog\n"; exit(0); }
$c = $r['content'];
$needle = 'id="blogCards"';
$pos = strpos($c, $needle);
if ($pos === false) {
    echo "No blogCards container found in stored content.\n";
    echo "Content length: " . strlen($c) . " bytes\n";
    exit(0);
}
$start = max(0, $pos - 400);
$snippet = substr($c, $start, 4000);
// sanitize for terminal
$snippet = str_replace("\t", ' ', $snippet);
$snippet = str_replace("\n", ' ', $snippet);
$snippet = preg_replace('/\s+/', ' ', $snippet);
echo "Found blogCards at offset: $pos\n";
echo "--- snippet (truncated) ---\n";
echo $snippet . "\n";
echo "--- end ---\n";
?>
