<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
    $pdo = Database::pdo();
    $slug = 'participacion-en-colombia-40';
    $title = 'Participación en Colombia 4.0';
    $excerpt = 'Presentación institucional y pitch que destacan el impacto cultural y técnico de Xlerion.';
    $content = '<h2>Participación en Colombia 4.0</h2><p>Presentación institucional y pitch que destacan el impacto cultural y técnico de Xlerion.</p>';
    $meta = json_encode(['source'=>'script','created'=>date('c')]);
    $st = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_at,updated_at) VALUES (?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $st->execute([$slug,$title,$excerpt,$content,$meta,1]);
    echo "Inserted page with slug: $slug\n";
} catch (Exception $e){
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
