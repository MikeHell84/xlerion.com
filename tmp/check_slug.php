<?php
require_once __DIR__ . '/../src/Model/Database.php';
$term = $argv[1] ?? 'postulacion-a-cocrea-2025';
try{
    $pdo = Database::pdo();
    // exact match
    $st = $pdo->prepare('SELECT slug,title,is_published FROM cms_pages WHERE slug = ? LIMIT 1');
    $st->execute([$term]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "FOUND\n".json_encode($row, JSON_PRETTY_PRINT)."\n";
        exit(0);
    }
    // try fuzzy search by term in slug or title
    $st2 = $pdo->prepare('SELECT slug,title,is_published FROM cms_pages WHERE slug LIKE ? OR title LIKE ? LIMIT 20');
    $like = '%'.str_replace('%','',$term).'%';
    $st2->execute([$like,$like]);
    $rows = $st2->fetchAll(PDO::FETCH_ASSOC);
    if ($rows && count($rows)>0){
        echo "NO EXACT MATCH. Nearby results:\n".json_encode($rows, JSON_PRETTY_PRINT)."\n";
        exit(0);
    }
    echo "NOT FOUND\n";
} catch (Exception $e){
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(2);
}
