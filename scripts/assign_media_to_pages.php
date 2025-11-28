<?php
require_once __DIR__ . '/../src/Model/Database.php';

$rootMedia = realpath(__DIR__ . '/../../media');
$publicMedia = realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR . 'media';
if ($rootMedia === false) { echo "Source media folder not found\n"; exit(1); }
if (!is_dir($publicMedia)) { mkdir($publicMedia, 0755, true); }

$images = [];
$it = new DirectoryIterator($rootMedia);
$allowed = ['jpg','jpeg','png','gif','webp','svg'];
foreach ($it as $f) {
    if ($f->isFile()) {
        $ext = strtolower(pathinfo($f->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) $images[] = $f->getFilename();
    }
}
if (empty($images)) { echo "No images found in media folder: $rootMedia\n"; exit(1); }

// pages to assign
$pages = [
    'el-origen-de-total-darkness',
    'filosofia-modular-videojuegos',
    'diagnostico-tecnico-cultural',
];

$pdo = Database::pdo();
$used = [];
$imgIndex = 0;
foreach ($pages as $slug) {
    $st = $pdo->prepare('SELECT id,content,meta FROM cms_pages WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    $r = $st->fetch(PDO::FETCH_ASSOC);
    if (!$r) { echo "Page not found: $slug\n"; continue; }
    $content = $r['content'] ?? '';
    // count subsections by <h3>
    preg_match_all('/<h3[^>]*>.*?<\/h3>/is', $content, $mh3);
    $count = max(1, count($mh3[0]));
    $assigned = [];
    for ($i=0;$i<$count;$i++) {
        // pick next unused image, wrap-around if necessary
        while (isset($used[$images[$imgIndex]])) { $imgIndex = ($imgIndex + 1) % count($images); }
        $src = $rootMedia . DIRECTORY_SEPARATOR . $images[$imgIndex];
        $dstName = $slug . '-' . ($i+1) . '-' . $images[$imgIndex];
        $dst = $publicMedia . DIRECTORY_SEPARATOR . $dstName;
        if (!file_exists($dst)) {
            copy($src, $dst);
        }
        $assigned[] = '/media/' . $dstName;
        $used[$images[$imgIndex]] = true;
        $imgIndex = ($imgIndex + 1) % count($images);
    }
    // update meta
    $meta = [];
    if (!empty($r['meta'])) { $tmp = json_decode($r['meta'], true); if (is_array($tmp)) $meta = $tmp; }
    $meta['section_images'] = $assigned;
    $u = $pdo->prepare('UPDATE cms_pages SET meta = ? WHERE id = ?');
    $u->execute([json_encode($meta), $r['id']]);
    echo "Assigned " . count($assigned) . " images to $slug -> " . implode(',', $assigned) . "\n";
}

echo "Done. Copied images to: $publicMedia\n";
