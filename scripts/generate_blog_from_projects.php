<?php
/**
 * Generate blog pages from project JSON files when there's no dedicated blog content.
 * Scans ../xlerion_ultimate_web/XlerionStoryCreator for JSON project definitions and upserts
 * into cms_pages (title, slug, excerpt/content).
 */

require_once __DIR__ . '/../src/Model/Database.php';

$projectDir = __DIR__ . '/../../xlerion_ultimate_web/XlerionStoryCreator';
if (!is_dir($projectDir)) {
    echo "Project dir not found: $projectDir\n";
    exit(1);
}

$files = glob($projectDir . '/*.json');
if (!$files) {
    echo "No project JSON files found in $projectDir\n";
    exit(0);
}

function slugify($s) {
    $s = mb_strtolower($s);
    $s = preg_replace('/[\s_]+/u', '-', $s);
    $s = preg_replace('/[^a-z0-9\-]+/u', '', iconv('UTF-8', 'ASCII//TRANSLIT', $s));
    $s = preg_replace('/-+/','-',$s);
    return trim($s, '-');
}

$pdo = Database::pdo();
$pdo->beginTransaction();
try {
    foreach ($files as $f) {
        $raw = @file_get_contents($f);
        if ($raw === false) { echo "Failed to read $f\n"; continue; }
        $data = @json_decode($raw, true);
        if (!is_array($data)) { echo "Invalid JSON: $f\n"; continue; }

        $title = $data['name'] ?? ($data['title'] ?? basename($f, '.json'));
        $desc = $data['description'] ?? ($data['summary'] ?? '');
        $slug = $data['slug'] ?? slugify($title);

        // Build simple HTML content: title + first paragraph of description
        $excerpt = '';
        if ($desc) {
            // take first 300 chars as excerpt
            $excerpt = trim(mb_substr(strip_tags($desc), 0, 320));
            if (mb_strlen($desc) > 320) $excerpt .= '...';
        }

        $content = '<h2>' . htmlspecialchars($title) . '</h2>\n';
        if ($excerpt) $content .= '<p class="text-muted">' . nl2br(htmlspecialchars($excerpt)) . '</p>\n';
        if (!empty($data['features']) && is_array($data['features'])) {
            $content .= '<h3>Características</h3>\n<ul>';
            foreach ($data['features'] as $feat) $content .= '<li>' . htmlspecialchars($feat) . '</li>';
            $content .= '</ul>\n';
        }

        // Upsert into cms_pages
        $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $meta = json_encode(['generated_from'=>'projects','source_file'=>basename($f),'generated_at'=>date('c')]);
        if ($row) {
            $u = $pdo->prepare('UPDATE cms_pages SET title = ?, content = ?, meta = ?, is_published = 1, updated_at = ? WHERE id = ?');
            $u->execute([$title, $content, $meta, date('c'), $row['id']]);
            echo "Updated project page: $slug (id={$row['id']})\n";
        } else {
            $i = $pdo->prepare('INSERT INTO cms_pages (title, slug, content, meta, is_published, created_at) VALUES (?,?,?,?,1,?)');
            $i->execute([$title, $slug, $content, $meta, date('c')]);
            echo "Inserted project page: $slug\n";
        }
    }

    $pdo->commit();
    echo "Generation finished.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
