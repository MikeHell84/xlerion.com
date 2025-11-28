<?php
$contenidoFile = __DIR__ . '/../../contenido.txt';
if (!file_exists($contenidoFile)) {
    echo "NO_FILE\n";
    exit(1);
}
$ct = file_get_contents($contenidoFile);
$pos = stripos($ct, 'Proyectos destacados');
if ($pos === false) {
    echo "NO_HEADER\n";
    exit(0);
}
$after = substr($ct, $pos + strlen('Proyectos destacados'));
// Split by double newlines to separate logical project blocks
$blocks = preg_split('/\r?\n\s*\r?\n/', trim($after));
$projTitles = [];
if (!empty($blocks)) {
    foreach ($blocks as $b) {
        $b = trim($b);
        if ($b === '') continue;
        // use the first line as title if block is long
        $firstLine = preg_split('/\r?\n/', $b)[0];
        $firstLine = trim(preg_replace('/^[\-\*\•\d\.\)\s]+/', '', $firstLine));
        if ($firstLine !== '') $projTitles[] = $firstLine;
        if (count($projTitles) >= 20) break;
    }
}
// fallback: line-by-line if blocks parsing failed
if (empty($projTitles)) {
    $lines = preg_split('/\r?\n/', $after);
    foreach ($lines as $ln) {
        $ln = trim($ln);
        if ($ln === '') {
            if (!empty($projTitles)) break;
            continue;
        }
        $ln = preg_replace('/^[\-\*\•\d\.\)\s]+/', '', $ln);
        if ($ln === '') continue;
        $projTitles[] = $ln;
        if (count($projTitles) >= 20) break;
    }
}
if (empty($projTitles)) {
    echo "NO_PROJECTS\n";
    exit(0);
}
foreach ($projTitles as $p) {
    echo $p . "\n";
}

