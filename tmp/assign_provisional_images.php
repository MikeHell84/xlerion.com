<?php
require_once __DIR__ . '/../src/Model/Database.php';
$base = __DIR__ . '/../../media/images/parallax';
if (!is_dir($base)) { echo "Parallax dir not found: $base\n"; exit(1); }
$files = array_values(array_filter(scandir($base), function($f){ return preg_match('/\.(jpg|jpeg|png|webp|gif)$/i',$f); }));
if (empty($files)) { echo "No parallax images found in $base\n"; exit(1); }
$pdo = Database::pdo();
$posts = $pdo->query('SELECT id,slug,meta FROM blog_posts ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$i=0; $assigned = 0;
foreach($posts as $p){
  $m = json_decode($p['meta'] ?? '{}', true) ?: [];
  if (!empty($m['image'])) continue;
  $pick = $files[$i % count($files)]; $i++;
  $url = '/media/images/parallax/' . $pick;
  $m['image'] = $url;
  $pdo->prepare('UPDATE blog_posts SET meta = ? WHERE id = ?')->execute([json_encode($m), $p['id']]);
  $assigned++; echo "Assigned {$url} to {$p['slug']}\n";
}

echo "\nAssigned images to {$assigned} posts.\n";
