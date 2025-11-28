<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$st = $pdo->prepare('SELECT content, meta FROM cms_pages WHERE slug = ? LIMIT 1');
$st->execute(['blog']);
$r = $st->fetch(PDO::FETCH_ASSOC);
if (!$r) { echo "NO_BLOG_ROW\n"; exit(0); }
$content = $r['content'];
$meta = $r['meta'];
echo "META: " . $meta . "\n";
$len = strlen($content);
echo "CONTENT_LEN: $len\n";
$cnt = preg_match_all('/class="[^\"]*blog-card-wrap[^\"]*"/i', $content, $m);
echo "CARD_WRAP_COUNT: " . ($cnt ?: 0) . "\n";
$cnt2 = substr_count($content, 'blog-card-wrap');
echo "CARD_WRAP_COUNT_SUBSTR: $cnt2\n";
// Print a small excerpt around the first card occurrence
$pos = strpos($content, 'blog-card-wrap');
if ($pos !== false) {
    $start = max(0, $pos - 120);
    $excerpt = substr($content, $start, 400);
    echo "EXCERPT_AROUND_FIRST_CARD:\n" . $excerpt . "\n";
} else {
    echo "NO_CARD_SNIPPET_FOUND\n";
}
// Also output titles found
if (preg_match_all('/<h5[^>]*>(.*?)<\/h5>/is', $content, $titles)) {
    echo "FOUND_TITLES_COUNT: " . count($titles[1]) . "\n";
    foreach ($titles[1] as $i => $t) {
        echo "TITLE_" . ($i+1) . ": " . strip_tags($t) . "\n";
    }
} else {
    echo "NO_TITLES_FOUND\n";
}
