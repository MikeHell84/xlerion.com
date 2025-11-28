<?php
require_once __DIR__ . '/../src/Model/Database.php';

$path = __DIR__ . '/../../contenido.txt';
$txt = @file_get_contents($path);
if ($txt === false) { echo "Failed to read $path\n"; exit(1); }

// Titles we want to import (title in file => slug in cms_pages)
$map = [
    // primary title => slug
    'Total Darkness – Pelijuego Interactivo' => 'el-origen-de-total-darkness',
    'Aplicación de la filosofía modular en videojuegos' => 'filosofia-modular-videojuegos',
    'Diagnóstico técnico como herramienta cultural' => 'diagnostico-tecnico-cultural',
];

// helper: normalize string for fuzzy matching (remove accents, punctuation, lowercase)
function normalize_for_search($s) {
    // convert to NFD and strip diacritics if possible
    $s = mb_strtolower($s);
    // replace common punctuation with space
    $s = preg_replace('/[\p{P}\p{S}]+/u', ' ', $s);
    // normalize whitespace
    $s = preg_replace('/\s+/u', ' ', $s);
    // remove accents by transliteration if available
    if (class_exists('Transliterator')) {
        try { $s = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC')->transliterate($s); } catch (Exception $e) {}
    } else {
        // basic replacement map as fallback
        $map = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u'];
        $s = strtr($s, $map);
    }
    return trim($s);
}

// pre-normalize the source text to speed up fuzzy search
$normalizedTxt = normalize_for_search($txt);

$pdo = Database::pdo();
$pdo->beginTransaction();
try {
    foreach ($map as $title => $slug) {
        // fuzzy locate title: try exact first, then normalized token search
        $pos = mb_stripos($txt, $title);
        if ($pos === false) {
            $nTitle = normalize_for_search($title);
            // direct normalized search
            $pos = mb_stripos($normalizedTxt, $nTitle);
            if ($pos === false) {
                // extract candidate heading lines from the original file (lines that look like headings)
                $lines = preg_split('/\r?\n/', $txt);
                $best = null; $bestScore = 9999; $cPos = false;
                foreach ($lines as $lnIdx => $line) {
                    $ln = trim($line);
                    if ($ln === '') continue;
                    // consider lines that are short-ish and contain letters (likely headings)
                    if (mb_strlen($ln) > 3 && mb_strlen($ln) < 120) {
                        $cand = normalize_for_search($ln);
                        // levenshtein on ascii fallback - use similar_text for multibyte friendliness
                        $score = levenshtein(substr($cand,0,200), substr($nTitle,0,200));
                        if ($score < $bestScore) { $bestScore = $score; $best = $ln; $cPos = $lnIdx; }
                    }
                }
                // if we found a close candidate, consider it a match when score is small enough
                if ($best !== null && $bestScore <= max(3, (int) (mb_strlen($nTitle) * 0.35))) {
                    // map line index back to an approximate byte offset by summing lengths
                    $offset = 0; for ($i = 0; $i < $cPos; $i++) { $offset += mb_strlen($lines[$i]) + 1; }
                    $pos = $offset;
                } else {
                    // last resort: try strpos of the main words
                    $parts = preg_split('/\s+/', $nTitle);
                    $found = false;
                    foreach ($parts as $p) {
                        if (mb_strlen($p) < 4) continue;
                        $pp = mb_stripos($normalizedTxt, $p);
                        if ($pp !== false) { $pos = $pp; $found = true; break; }
                    }
                    if (!$found) $pos = false;
                }
            }
        }

        if ($pos === false || $pos === null) {
            echo "Title not found: $title\n";
            continue;
        }

        // from pos, try to extract a block: look ahead 1200 chars or until next '💡' or section header (emoji+space or two newlines followed by an emoji or an all caps word)
        $start = max(0, $pos - 20);
        $rest = mb_substr($txt, $start, 3000);
        if (preg_match('/' . preg_quote($title, '/') . '/u', $rest, $m, PREG_OFFSET_CAPTURE)) {
            $offset = $m[0][1] + mb_strlen($m[0][0]);
            $rest = mb_substr($rest, $offset);
        } else {
            // fallback: use rest as-is
            $rest = mb_substr($txt, $pos + mb_strlen($title));
        }

        if (preg_match('/([\s\S]*?)(?=\n\n[\p{So}]|\n\n[A-Z0-9\-].+?:|\n\n\z)/u', $rest, $m)) {
            $block = trim($m[1]);
        } else {
            $block = trim(mb_substr($rest, 0, 1600));
        }

        // split into paragraphs and headings
        $parts = preg_split('/\n\s*\n/u', $block);
        $html = "<h2>" . htmlspecialchars($title) . "</h2>\n";
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            // If line looks like a short heading (ends with :) make it a h3
            if (preg_match('/^([A-ZÁÉÍÓÚÑ][^\n]{1,120}):\s*$/u', $p, $h)) {
                $html .= '<h3>' . htmlspecialchars(trim($h[1])) . '</h3>\n';
                continue;
            }
            $html .= '<p>' . nl2br(htmlspecialchars($p)) . '</p>\n';
        }

        // upsert into cms_pages by slug
        $stmt = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $meta = json_encode(['imported_from'=>'contenido.txt','imported_at'=>date('c')]);
        if ($row) {
            $u = $pdo->prepare('UPDATE cms_pages SET title = ?, content = ?, meta = ?, is_published = 1, updated_at = ? WHERE id = ?');
            $u->execute([$title, $html, $meta, date('c'), $row['id']]);
            echo "Updated {$slug} (id={$row['id']})\n";
        } else {
            $i = $pdo->prepare('INSERT INTO cms_pages (title, slug, content, meta, is_published, created_at) VALUES (?,?,?,?,1,?)');
            $i->execute([$title, $slug, $html, $meta, date('c')]);
            echo "Inserted {$slug}\n";
        }
    }

    // Optionally update the main blog page to include links (keep existing index generator separate)
    $pdo->commit();
    echo "Import finished.\n";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

return 0;
