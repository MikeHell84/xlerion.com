<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$slugs = ['blog','el-origen-de-total-darkness','filosofia-modular-videojuegos','diagnostico-tecnico-cultural'];
foreach ($slugs as $s) {
    $st = $pdo->prepare('SELECT id,title,slug,content,meta FROM cms_pages WHERE slug = ? LIMIT 1');
    $st->execute([$s]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    echo "--- PAGE: " . ($r['slug'] ?? $s) . " (id:" . ($r['id'] ?? 'n/a') . ") ---\n";
    if (!$r) { echo "NOT FOUND\n\n"; continue; }
    echo "Title: " . ($r['title'] ?? '') . "\n";
    echo "Meta: " . ($r['meta'] ?? '') . "\n";
    echo "Content preview (first 800 chars):\n" . substr($r['content'] ?? '',0,800) . "\n\n";
}
