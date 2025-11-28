<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$st = $pdo->prepare('SELECT id,content FROM cms_pages WHERE slug=? LIMIT 1');
$st->execute(['blog']);
$r = $st->fetch(PDO::FETCH_ASSOC);
file_put_contents(__DIR__ . '/backup_blog_content.html', $r['content'] ?? '');
echo "backup written\n";
