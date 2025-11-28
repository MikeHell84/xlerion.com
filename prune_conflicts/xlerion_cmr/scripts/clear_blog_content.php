<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$u = $pdo->prepare('UPDATE cms_pages SET content = ?, meta = ? WHERE slug = ?');
$meta = json_encode(['cleared_from_contenido' => true, 'cleared_at' => date('c')]);
$u->execute(['', $meta, 'blog']);
echo "blog content cleared\n";
