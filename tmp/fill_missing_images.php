<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();

$posts = $pdo->query('SELECT id,slug,meta FROM blog_posts ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
$found = 0; $failed = [];
foreach($posts as $p){
  $m = json_decode($p['meta'] ?? '{}', true) ?: [];
  if (!empty($m['image'])) continue; // already has image

  // try cms_pages meta
  $st = $pdo->prepare('SELECT content,meta,is_published FROM cms_pages WHERE slug = ? LIMIT 1'); $st->execute([$p['slug']]); $pg = $st->fetch(PDO::FETCH_ASSOC);
  $img = null;
  if ($pg){
    $pm = json_decode($pg['meta'] ?? '{}', true) ?: [];
    if (!empty($pm['media_url'])) $img = $pm['media_url'];
    // search common patterns in content
    if (!$img && !empty($pg['content'])){
      // <img src="...">
      if (preg_match('/<img[^>]+src=["\']([^"\']+\.(?:jpg|jpeg|png|webp|gif))["\']/i',$pg['content'],$mch)) $img = $mch[1];
      // url('/media/xxx.jpg') or background-image: url(/media/xxx.png)
      if (!$img && preg_match('/url\(["\']?([^"\')]+\.(?:jpg|jpeg|png|webp|gif))["\']?\)/i',$pg['content'],$mch2)) $img = $mch2[1];
      // plain /media/...jpg
      if (!$img && preg_match('#(/media/[^"\s\)]+\.(?:jpg|jpeg|png|webp|gif))#i',$pg['content'],$mch3)) $img = $mch3[1];
    }
  }

  if ($img){
    $postMeta = []; if (!empty($p['meta'])) $postMeta = json_decode($p['meta'], true) ?: [];
    $postMeta['image'] = $img;
    $u = $pdo->prepare('UPDATE blog_posts SET meta = ? WHERE id = ?'); $u->execute([json_encode($postMeta), $p['id']]);
    $found++; echo "Set image for {$p['slug']} -> {$img}\n";
    // also set published if cms page indicates published
    if ($pg && $pg['is_published']){
      $pdo->prepare('UPDATE blog_posts SET status = ? WHERE id = ?')->execute(['published', $p['id']]);
    }
  } else {
    $failed[] = $p['slug'];
  }
}

echo "\nImages set: {$found}, failed: " . count($failed) . "\n";
if (!empty($failed)) echo implode("\n", $failed) . "\n";
