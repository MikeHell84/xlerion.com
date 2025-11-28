<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$rows = $pdo->query('SELECT id,slug,status,published_at,meta FROM blog_posts ORDER BY id ASC LIMIT 60')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r){
  $m = json_decode($r['meta']??'{}', true) ?: [];
  echo sprintf("id=%s slug=%s status=%s published_at=%s image=%s\n", $r['id'],$r['slug'],$r['status'],$r['published_at'],$m['image']??'');
}
