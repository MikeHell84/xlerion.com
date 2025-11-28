<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$targets = [
    'filosof-a',
    'filosofia-modular-videojuegos'
];
$updated = [];
foreach ($targets as $slug) {
    $stmt = $pdo->prepare('SELECT id, meta FROM cms_pages WHERE slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) continue;
    $meta = [];
    if (!empty($row['meta'])) {
        $tmp = json_decode($row['meta'], true);
        if (is_array($tmp)) $meta = $tmp;
    }
    $meta['menu_group'] = 'Información';
    $metaJson = json_encode($meta, JSON_UNESCAPED_UNICODE);
    $u = $pdo->prepare('UPDATE cms_pages SET meta = :meta WHERE id = :id');
    $u->execute([':meta' => $metaJson, ':id' => $row['id']]);
    $updated[] = ['id' => $row['id'], 'slug' => $slug];
}
echo json_encode(['updated' => $updated], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
