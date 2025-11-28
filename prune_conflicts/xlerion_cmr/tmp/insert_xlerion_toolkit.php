<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
    $pdo = Database::pdo();
    $slug = 'xlerion-toolkit';
    $title = 'Xlerion Toolkit';
    $excerpt = 'Recursos y herramientas Xlerion Toolkit.';
    $content = '<h2>Xlerion Toolkit</h2><p>Herramientas y recursos de ejemplo.</p>';
    $meta = json_encode(['source'=>'script','created'=>date('c')]);
    $st = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_at,updated_at) VALUES (?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $st->execute([$slug,$title,$excerpt,$content,$meta,1]);
    echo "Inserted page with slug: $slug\n";
} catch (Exception $e){
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
