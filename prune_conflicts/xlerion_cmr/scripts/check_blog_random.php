<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$st = $pdo->prepare('SELECT content FROM cms_pages WHERE slug = ? LIMIT 1');
$st->execute(['blog']);
$r = $st->fetch(PDO::FETCH_ASSOC);
if (!$r) {
    echo "No row for slug=blog\n";
    exit(0);
}
$c = $r['content'];
$needle = 'Entrada de prueba';
$pos = strpos($c, $needle);
if ($pos === false) {
    echo "No marker 'Entrada de prueba' found in blog content.\n";
    // For debug, print small tail of content length
    echo "Content length: " . strlen($c) . " bytes\n";
    exit(0);
}
$start = max(0, $pos - 120);
$snippet = substr($c, $start, 360);
echo "Found marker at offset: $pos\n";
echo "--- snippet ---\n";
echo $snippet . "\n";
echo "--- end ---\n";
?>
