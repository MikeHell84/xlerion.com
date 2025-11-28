<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$rows = $pdo->query("SELECT id,slug,title,meta FROM cms_pages WHERE is_published=1 ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
$result = [];
foreach ($rows as $r) {
    $meta = [];
    if (!empty($r['meta'])) { $tmp = json_decode($r['meta'], true); if (is_array($tmp)) $meta = $tmp; }
    $group = isset($meta['menu_group']) ? $meta['menu_group'] : null;
    // fallback groupsDef from layout.php
    $groupsDef = [
        'Información' => ['slugs' => ['inicio','filosofia','acerca-del-creador','legal'], 'icon' => 'info'],
        'Servicios' => ['slugs' => ['servicios','soluciones','proyectos'], 'icon' => 'build'],
        'Recursos' => ['slugs' => ['documentacion','blog','convocatorias-alianzas'], 'icon' => 'menu_book'],
        'Contacto' => ['slugs' => ['contact','contacto'], 'icon' => 'mail']
    ];
    if (!$group) {
        $assigned = null;
        foreach ($groupsDef as $gTitle => $gDef) { if (in_array($r['slug'], $gDef['slugs'], true)) { $assigned = $gTitle; break; } }
        $group = $assigned ?: 'Otros';
    }
    if ($group === 'Otros') $result[] = ['id'=>$r['id'],'slug'=>$r['slug'],'title'=>$r['title'],'meta'=>$meta];
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
