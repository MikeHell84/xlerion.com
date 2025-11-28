<?php
require_once __DIR__ . '/../src/Model/Database.php';
$from = 'documentacion';
$to = $argv[1] ?? 'la-documentacion-en-xlerion-es-un-pilar-fundamental-que-asegura-la-continuidad-replicabilidad-y-evolucion-de-cada-solucion-tecnica-a-continuacion-profundizamos-en-los-elementos-clave-que-conforman-nuestro-enfoque-documental';
try{
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT * FROM cms_pages WHERE slug = ? LIMIT 1');
    $st->execute([$from]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo "Source slug not found: $from\n"; exit(1); }
    $exists = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
    $exists->execute([$to]);
    if ($exists->fetch()) { echo "Target slug already exists: $to\n"; exit(0); }
    $ins = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_by,updated_by,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $ins->execute([$to, $row['title'], $row['excerpt'], $row['content'], $row['meta'], $row['is_published'], $row['created_by'] ?? null, $row['updated_by'] ?? null]);
    echo "Cloned $from -> $to\n";
} catch (Exception $e){ echo 'ERROR: ' . $e->getMessage() . "\n"; }
