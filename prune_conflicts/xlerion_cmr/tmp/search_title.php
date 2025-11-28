<?php
require_once __DIR__ . '/../src/Model/Database.php';
$term = $argv[1] ?? 'Colombia';
try{
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT slug,title FROM cms_pages WHERE title LIKE ? OR excerpt LIKE ? LIMIT 50');
    $like = '%'.$term.'%';
    $st->execute([$like,$like]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) { echo "No matches for term: $term\n"; exit(0); }
    echo json_encode($rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)."\n";
} catch (Exception $e){ echo 'ERROR: '.$e->getMessage()."\n"; }
