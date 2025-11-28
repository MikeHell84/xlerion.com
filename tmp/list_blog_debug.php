<?php
require_once __DIR__ . '/../src/Model/Database.php';
try{
  $pdo = Database::pdo();
}catch(Exception $e){ echo "DB error: " . $e->getMessage() . "\n"; exit(1); }

echo "Checking blog_posts table...\n";
try{ $c = $pdo->query('SELECT COUNT(*) as cnt FROM blog_posts')->fetchColumn(); echo "blog_posts rows: " . intval($c) . "\n"; } catch(Exception $e){ echo "blog_posts table missing or error: " . $e->getMessage() . "\n"; }

echo "\nListing recent cms_pages entries that look like blog posts (slug contains 'blog' or published recently)\n";
try{
  $rows = $pdo->query("SELECT id,slug,title,is_published,created_at FROM cms_pages ORDER BY created_at DESC LIMIT 30")->fetchAll();
  foreach($rows as $r){ echo sprintf("cms_pages id=%s slug=%s title=%s published=%s created=%s\n", $r['id'],$r['slug'],$r['title'],$r['is_published'],$r['created_at']); }
}catch(Exception $e){ echo "cms_pages query error: " . $e->getMessage() . "\n"; }

echo "\nSample blog_posts rows (if table exists):\n";
try{
  $posts = $pdo->query('SELECT id,title,slug,status,published_at FROM blog_posts ORDER BY published_at DESC LIMIT 20')->fetchAll();
  foreach($posts as $p){ echo sprintf("post id=%s slug=%s title=%s status=%s published=%s\n", $p['id'],$p['slug'],$p['title'],$p['status'],$p['published_at']); }
}catch(Exception $e){ echo "blog_posts select error: " . $e->getMessage() . "\n"; }

echo "\nDone.\n";
