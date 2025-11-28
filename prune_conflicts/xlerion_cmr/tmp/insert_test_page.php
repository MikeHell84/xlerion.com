<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
    $pdo = Database::pdo();
    $slug = 'postulacion-a-cocrea-2025';
    $title = 'Postulación a CoCrea 2025';
    $excerpt = 'Convocatoria y información para participar en CoCrea 2025.';
    $content = '<h2>Postulación a CoCrea 2025</h2><p>Contenido de ejemplo para la convocatoria.</p>';
    $meta = json_encode(['source'=>'script','created'=>date('c')]);
    $st = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_at,updated_at) VALUES (?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $st->execute([$slug,$title,$excerpt,$content,$meta,1]);
    echo "Inserted page with slug: $slug\n";
} catch (Exception $e){
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
