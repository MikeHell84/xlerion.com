<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
// Fetch the blog page content (generated index) from cms_pages
$st = $pdo->prepare('SELECT content FROM cms_pages WHERE slug = ? LIMIT 1');
$st->execute(['blog']);
$r = $st->fetch(PDO::FETCH_ASSOC);
if (!$r) { echo "No blog page row found. Run scripts/populate_blog_index.php first\n"; exit(1); }
$html = $r['content'];
// find hrefs like /blog/slug and /slug
preg_match_all('/href="\/(blog\/)?([a-z0-9\-]+)"/i', $html, $m);
$slugs = array_unique($m[2]);
$created = [];
foreach ($slugs as $s){
    if (!$s) continue;
    // skip 'blog' itself
    if ($s === 'blog') continue;
    $chk = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
    $chk->execute([$s]);
    if ($chk->fetch()) continue;
    $title = ucwords(str_replace('-', ' ', $s));
    $excerpt = 'Entrada generada automáticamente para slug ' . $s;
    $content = '<h2>' . htmlspecialchars($title) . '</h2><p>Contenido placeholder generado automáticamente.</p>';
    $meta = json_encode(['generated_from'=>'repair_script','generated_at'=>date('c')]);
    $ins = $pdo->prepare('INSERT INTO cms_pages (slug,title,excerpt,content,meta,is_published,created_at,updated_at) VALUES (?,?,?,?,?,?,datetime("now"),datetime("now"))');
    $ins->execute([$s,$title,$excerpt,$content,$meta,1]);
    $created[] = $s;
}
if (empty($created)) echo "No missing slugs found\n";
else echo "Created slugs:\n".implode("\n",$created)."\n";
