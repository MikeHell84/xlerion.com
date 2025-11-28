<?php
// Migrate selected cms_pages into blog_posts table
require_once __DIR__ . '/../src/Model/Database.php';
try{ $pdo = Database::pdo(); } catch(Exception $e){ echo "DB error: " . $e->getMessage() . "\n"; exit(1); }

// Ensure blog_posts table exists (SQLite friendly)
try{
  $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug VARCHAR(191) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content TEXT NULL,
    author_id INTEGER NULL,
    status VARCHAR(50) DEFAULT 'draft',
    published_at DATETIME NULL,
    views INTEGER DEFAULT 0,
    meta TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  )");
  echo "Ensured blog_posts table exists.\n";
}catch(Exception $e){ echo "Cannot create blog_posts: " . $e->getMessage() . "\n"; exit(1); }

// Find candidates: cms_pages created in last 30 days OR slugs that look like blog posts
$days = 30; $candStmt = $pdo->prepare("SELECT id,slug,title,excerpt,content,is_published,created_at FROM cms_pages WHERE datetime(created_at) >= datetime('now', ?)");
$candStmt->execute(["-{$days} days"]);
$candidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($candidates)){
  echo "No candidates found in last {$days} days. Trying fallback: select pages with 'blog' in slug.\n";
  $candStmt = $pdo->query("SELECT id,slug,title,excerpt,content,is_published,created_at FROM cms_pages WHERE slug LIKE '%blog%' LIMIT 200");
  $candidates = $candStmt->fetchAll(PDO::FETCH_ASSOC);
}

echo "Found " . count($candidates) . " candidate pages to consider for migration.\n";

$inserted = [];
foreach($candidates as $c){
  // Skip if page already marked migrated in meta
  $meta = [];
  if (!empty($c['meta'])){ $mm = json_decode($c['meta'], true); if (is_array($mm)) $meta = $mm; }
  if (!empty($meta['migrated_to_post_id'])){ echo "Skipping {$c['slug']} (already migrated)\n"; continue; }

  // Prepare fields
  $title = $c['title'] ?: substr($c['slug'],0,60);
  $slug = $c['slug'];
  $excerpt = $c['excerpt'] ?? null;
  $content = $c['content'] ?? null;
  $status = ($c['is_published']? 'published' : 'draft');
  $published_at = $c['created_at'] ?: date('Y-m-d H:i:s');
  $author_id = 3; // default admin

  // Insert into blog_posts if slug not exists
  try{
    $st = $pdo->prepare('SELECT id FROM blog_posts WHERE slug = ? LIMIT 1'); $st->execute([$slug]); $exists = $st->fetchColumn();
    if ($exists){ echo "Post with slug {$slug} already exists (id={$exists}), marking cms_pages meta.\n"; $newId = $exists; }
    else {
      $ins = $pdo->prepare('INSERT INTO blog_posts (title,slug,excerpt,content,author_id,status,published_at,created_at) VALUES (?,?,?,?,?,?,?,datetime("now"))');
      $ins->execute([$title,$slug,$excerpt,$content,$author_id,$status,$published_at]);
      $newId = $pdo->lastInsertId();
      echo "Inserted post id={$newId} slug={$slug}\n";
    }
    // update cms_pages.meta to reference migrated id
    $meta['migrated_to_post_id'] = $newId;
    $upd = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?');
    $upd->execute([json_encode($meta), $c['id']]);
    $inserted[] = ['slug'=>$slug,'post_id'=>$newId];
  }catch(Exception $e){ echo "Error migrating {$slug}: " . $e->getMessage() . "\n"; }
}

echo "\nMigration complete. Migrated count: " . count($inserted) . "\n";
if (!empty($inserted)){
  echo "List of migrated slugs:\n";
  foreach($inserted as $it) echo " - {$it['slug']} -> post_id={$it['post_id']}\n";
}

echo "Done.\n";
