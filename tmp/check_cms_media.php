<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$rows = $pdo->query('SELECT id,slug,title,meta,content FROM cms_pages ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r){
  $meta = json_decode($r['meta'] ?? '{}', true) ?: [];
  $hasMetaMedia = !empty($meta['media_url']) ? $meta['media_url'] : '';
  $hasImgTag = '';
  $hasMediaPath = '';
  if (!empty($r['content'])){
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i',$r['content'],$m)) $hasImgTag = $m[1];
    if (!$hasImgTag && preg_match('#(/media/[^"\s\)]+\.(?:jpg|jpeg|png|webp|gif))#i',$r['content'],$m2)) $hasMediaPath = $m2[1];
  }
  if ($hasMetaMedia || $hasImgTag || $hasMediaPath){
    echo sprintf("id=%s slug=%s meta_media=%s imgTag=%s mediaPath=%s\n", $r['id'],$r['slug'],$hasMetaMedia,$hasImgTag,$hasMediaPath);
  }
}
echo "Done.\n";
