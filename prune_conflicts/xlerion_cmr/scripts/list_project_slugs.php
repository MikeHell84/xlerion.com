<?php
// Lists project titles and slugs from XlerionStoryCreator JSON files
$projectDir = __DIR__ . '/../../xlerion_ultimate_web/XlerionStoryCreator';
function slugify($s) {
    $s = mb_strtolower($s);
    $s = preg_replace('/[\s_]+/u', '-', $s);
    $s = preg_replace('/[^a-z0-9\-]+/u', '', iconv('UTF-8', 'ASCII//TRANSLIT', $s));
    $s = preg_replace('/-+/', '-', $s);
    return trim($s, '-');
}
$results = [];
if (!is_dir($projectDir)) {
    fwrite(STDERR, "Project dir not found: $projectDir\n");
    exit(1);
}
foreach (glob($projectDir . '/*.json') as $f) {
    $raw = @file_get_contents($f);
    $data = @json_decode($raw, true);
    if (!$data) continue;
    // Support arrays at top-level (some JSONs here use array wrapping)
    $entry = null;
    if (isset($data['name'])) $entry = $data;
    elseif (is_array($data) && isset($data[0]) && is_array($data[0]) && isset($data[0]['name'])) $entry = $data[0];
    if (!$entry) {
        // fallback: try first object
        if (is_array($data) && isset($data[0]) && is_array($data[0])) $entry = $data[0];
    }
    if (!$entry) continue;
    $title = $entry['name'] ?? ($entry['title'] ?? basename($f, '.json'));
    $slug = $entry['slug'] ?? slugify($title);
    $results[] = ['title'=>$title, 'slug'=>$slug, 'file'=>basename($f)];
}
// Print human-friendly
foreach ($results as $r) {
    echo $r['title'] . " -> " . $r['slug'] . " (" . $r['file'] . ")\n";
}
// Print JSON for copy/paste
echo "\nJSON:\n";
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
?>
