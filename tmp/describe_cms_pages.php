<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
    $pdo = Database::pdo();
    $st = $pdo->query("PRAGMA table_info('cms_pages')");
    $cols = $st->fetchAll(PDO::FETCH_ASSOC);
    if (!$cols) { echo "Table cms_pages not found\n"; exit(1); }
    echo "Columns for cms_pages:\n";
    foreach($cols as $c){ echo $c['cid']." - ".$c['name']." (".$c['type'].")"."\n"; }
} catch (Exception $e){
    echo "ERROR: " . $e->getMessage() . "\n"; exit(2);
}
