<?php
require_once __DIR__ . '/../src/Model/Database.php';
$from = $argv[1] ?? 'el-origen-de-total-darkness';
$to = $argv[2] ?? 'total-darkness-pelijuego-interactivo';
try{
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM cms_pages WHERE slug = ? LIMIT 1');
    $st->execute([$from]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo "Source slug not found: $from\n"; exit(1); }
    // prepare insert (avoid duplicate slug)
    $exists = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
    $exists->execute([$to]);
    if ($exists->fetch()) { echo "Target slug already exists: $to\n"; exit(0); }
    $ins = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_by,updated_by,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $ins->execute([$to, $row['title'], $row['excerpt'], $row['content'], $row['meta'], $row['is_published'], $row['created_by'] ?? null, $row['updated_by'] ?? null]);
    echo "Cloned $from -> $to\n";
} catch (Exception $e){
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
