<?php
require_once __DIR__ . '/../src/Model/Database.php';
$txt = file_get_contents(__DIR__ . '/../../contenido.txt');
if ($txt === false) { echo "failed to read contenido.txt\n"; exit(1); }

// Split by top-level emoji headings (lines that start with an emoji and a space)
$parts = preg_split('/^([\p{So}].+)$/um', $txt, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
// parts will be [heading, body, heading, body, ...]
$entries = [];
for ($i=0;$i<count($parts);$i+=2) {
    $heading = trim($parts[$i]);
    $body = trim($parts[$i+1] ?? '');
    // remove emoji and possible trailing label
    $title = preg_replace('/^[\p{So}]\s*/u', '', $heading);
    $title = trim($title);
    if ($title === '') continue;
    // convert body paragraphs to HTML
    $paras = preg_split('/\n\s*\n/', trim($body));
    $html = '';
    foreach ($paras as $p) {
        $p = trim($p);
        if ($p === '') continue;
        $html .= '<p>' . htmlspecialchars($p) . '</p>\n';
    }
    $entries[] = ['title'=>$title, 'content'=>$html];
}

$pdo = Database::pdo();
$pdo->beginTransaction();
try {
    foreach ($entries as $e) {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i','-', $e['title']), '-'));
        $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $meta = json_encode(['generated_from_contenido'=>true,'generated_at'=>date('c')]);
        if ($row) {
            $u = $pdo->prepare('UPDATE cms_pages SET title = ?, content = ?, meta = ?, is_published = 1 WHERE id = ?');
            $u->execute([$e['title'], $e['content'], $meta, $row['id']]);
            echo "Updated page {$slug} (id={$row['id']})\n";
        } else {
            $ins = $pdo->prepare('INSERT INTO cms_pages (title, slug, content, meta, is_published, created_at) VALUES (?, ?, ?, ?, 1, ?)');
            $ins->execute([$e['title'], $slug, $e['content'], $meta, date('c')]);
            echo "Inserted page {$slug}\n";
        }
    }
    $pdo->commit();
    echo "Done\n";
} catch (Exception $ex) {
    $pdo->rollBack();
    echo 'Error: ' . $ex->getMessage() . "\n";
    exit(1);
}
