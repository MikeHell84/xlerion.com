<?php
require_once __DIR__ . '/../src/Model/Database.php';
$txt = file_get_contents(__DIR__ . '/../../contenido.txt');
if ($txt === false) { echo "failed to read contenido.txt\n"; exit(1); }

// Find the '🧩 Blog / Bitácora' heading and extract until next top-level heading (starts with \n\n🛡️ or similar)
$marker = '🧩 Blog / Bitácora';
$pos = mb_strpos($txt, $marker);
if ($pos === false) { echo "Blog marker not found\n"; exit(1); }
$rest = mb_substr($txt, $pos);
// Try to split by next top-level heading: lines that start with an emoji and then space + word (heuristic)
if (preg_match('/^(.+?)(?:\n\n[\p{So}].+|$)/us', $rest, $m)) {
    $block = trim($m[1]);
} else {
    $block = trim($rest);
}

// Clean up leading lines like '🧩 Blog / Bitácora' and 'Texto principal:'
$block = preg_replace('/^🧩\s*Blog\s*\/\s*Bitácora\s*/u', '', $block);
$block = preg_replace('/^Texto principal:\s*/u', '', $block);
$block = trim($block);

// Convert simple plaintext separators into HTML paragraphs for storage
$lines = preg_split('/\n\s*\n/', $block);
$html = '';
foreach ($lines as $ln) {
    $ln = trim($ln);
    if ($ln === '') continue;
    // if line looks like 'El origen de Total Darkness:' treat as heading
    if (preg_match('/^([A-ZÑÁÉÍÓÚ].+?):\s*$/u', $ln, $h)) {
        $html .= '<h2>' . htmlspecialchars(trim($h[1])) . '</h2>' . "\n";
        continue;
    }
    // otherwise wrap as paragraph
    $html .= '<p>' . htmlspecialchars($ln) . '</p>' . "\n";
}

// Update DB
$pdo = Database::pdo();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
    $stmt->execute(['blog']);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $u = $pdo->prepare('UPDATE cms_pages SET content = ?, meta = ? WHERE id = ?');
        $meta = json_encode(['restored_from_contenido'=>true,'restored_at'=>date('c')]);
        $u->execute([$html, $meta, $r['id']]);
        echo "Updated blog id={$r['id']}\n";
    } else {
        $i = $pdo->prepare('INSERT INTO cms_pages (title,slug,content,meta,is_published,created_at) VALUES (?,?,?,?,1,?)');
        $meta = json_encode(['restored_from_contenido'=>true,'restored_at'=>date('c')]);
        $i->execute(['Blog','blog',$html,$meta,date('c')]);
        echo "Inserted blog\n";
    }
    $pdo->commit();
    echo "Done\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
