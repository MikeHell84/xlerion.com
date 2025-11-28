<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{ $pdo = Database::pdo(); } catch(Exception $e){ echo "DB error: " . $e->getMessage() . "\n"; exit(1); }

// Select cms_pages that were migrated (have migrated_to_post_id in meta)
$rows = $pdo->query("SELECT id,slug,title,content,meta FROM cms_pages WHERE meta LIKE '%migrated_to_post_id%' LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($rows) . " migrated pages to process.\n";

foreach($rows as $r){
  $meta = json_decode($r['meta'] ?? '{}', true) ?: [];
  $postId = $meta['migrated_to_post_id'] ?? null;
  if (!$postId) { echo "Skipping {$r['slug']}: no migrated_to_post_id\n"; continue; }

  // Determine image: prefer meta.media_url stored in page meta
  $img = $meta['media_url'] ?? null;
  if (!$img){
    // try to extract first <img src="...">
    if (!empty($r['content'])){
      if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $r['content'], $m)){
        $img = $m[1];
      }
    }
  }

  // Determine excerpt: use existing excerpt in cms_pages.meta or first 200 chars of stripped content
  $excerpt = $meta['excerpt'] ?? null;
  if (!$excerpt && !empty($r['content'])){
    $txt = strip_tags($r['content']); $excerpt = mb_substr(trim(preg_replace('/\s+/',' ',$txt)),0,220);
  }

  // Load corresponding blog_post
  $st = $pdo->prepare('SELECT id,meta,excerpt FROM blog_posts WHERE id = ? LIMIT 1'); $st->execute([$postId]); $post = $st->fetch(PDO::FETCH_ASSOC);
  if (!$post){ echo "No blog_post for migrated id={$postId} (slug={$r['slug']})\n"; continue; }

  $postMeta = json_decode($post['meta'] ?? '{}', true) ?: [];
  $changed = false;
  if ($img && empty($postMeta['image'])){ $postMeta['image'] = $img; $changed = true; }
  if ($excerpt && empty($post['excerpt'])){ $upd = $pdo->prepare('UPDATE blog_posts SET excerpt = ? WHERE id = ?'); $upd->execute([$excerpt,$postId]); echo "Set excerpt for post_id={$postId}\n"; }
  if ($changed){ $u = $pdo->prepare('UPDATE blog_posts SET meta = ? WHERE id = ?'); $u->execute([json_encode($postMeta), $postId]); echo "Set image meta for post_id={$postId} -> {$postMeta['image']}\n"; }
}

echo "Done.\n";
