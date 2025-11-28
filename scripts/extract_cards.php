<?php
$path = __DIR__ . '/../../xlerion_cmr/public/..//..//..//..//x/temp_blog26.html';
// attempt likely path where temp file saved
$path = 'x:\\temp_blog26.html';
if (!file_exists($path)) {
    echo "NO_TEMP_FILE\n";
    exit(1);
}
$html = file_get_contents($path);
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
$xpath = new DOMXPath($dom);
$nodes = $xpath->query("//div[contains(@class,'blog-card-wrap')]");
if ($nodes->length === 0) {
    echo "NO_CARDS_FOUND\n";
    exit(0);
}
echo "FOUND " . $nodes->length . " cards\n";
for ($i = 0; $i < $nodes->length; $i++) {
    $node = $nodes->item($i);
    echo "---CARD " . ($i+1) . "---\n";
    // outer HTML
    echo $dom->saveHTML($node) . "\n\n";
}
