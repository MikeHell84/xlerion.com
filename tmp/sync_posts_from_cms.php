<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{ $pdo = Database::pdo(); } catch(Exception $e){ echo "DB error: " . $e->getMessage() . "\n"; exit(1); }

// Find blog_posts rows and ensure they have image/excerpt and published status when cms_pages indicates published
$posts = $pdo->query('SELECT id,title,slug,excerpt,status,meta FROM blog_posts ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($posts) . " blog_posts to check.\n";
$updated = 0; $noimage = [];
foreach($posts as $p){
  $slug = $p['slug'];
  // find corresponding cms_page
  $st = $pdo->prepare('SELECT id,is_published,content,meta,excerpt FROM cms_pages WHERE slug = ? LIMIT 1');
  $st->execute([$slug]); $pg = $st->fetch(PDO::FETCH_ASSOC);
  $needUpdate = false; $updateFields = [];
  // if cms_page exists, prefer its media/excerpt and published flag
  if ($pg){
    // set published if page is published
    if ($pg['is_published'] && $p['status'] !== 'published') { $updateFields['status']='published'; $needUpdate = true; }
    // determine image
    $pageMeta = json_decode($pg['meta'] ?? '{}', true) ?: [];
    $img = $pageMeta['media_url'] ?? null;
    if (!$img){ if (!empty($pg['content']) && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i',$pg['content'],$m)) $img = $m[1]; }
    $postMeta = json_decode($p['meta'] ?? '{}', true) ?: [];
    if ($img && empty($postMeta['image'])){ $postMeta['image'] = $img; $updateFields['meta'] = json_encode($postMeta); $needUpdate = true; }
    // excerpt fallback
    if (empty($p['excerpt'])){
      $ex = $pg['excerpt'] ?? null;
      if (!$ex && !empty($pg['content'])){ $ex = mb_substr(trim(preg_replace('/\s+/',' ',strip_tags($pg['content']))),0,220); }
      if ($ex){ $updateFields['excerpt'] = $ex; $needUpdate = true; }
    }
  } else {
    // no cms_page found — note
    // if meta contains image, ok; else mark
    $pm = json_decode($p['meta'] ?? '{}', true) ?: [];
    if (empty($pm['image'])) $noimage[] = $slug;
  }

  if ($needUpdate && !empty($updateFields)){
    // build SQL
    $sets = [];$vals = [];
    foreach($updateFields as $k=>$v){ $sets[] = "$k = ?"; $vals[] = $v; }
    $vals[] = $p['id'];
    $sql = 'UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id = ?';
    try{ $pdo->prepare($sql)->execute($vals); $updated++; echo "Updated post slug={$slug}\n"; } catch(Exception $e){ echo "Error updating {$slug}: " . $e->getMessage() . "\n"; }
  }
}

echo "\nSync complete. Updated: {$updated}. Posts without image (not matched): " . count($noimage) . "\n";
if (!empty($noimage)) echo implode("\n", $noimage) . "\n";
