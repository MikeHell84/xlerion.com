<?php
require_once __DIR__ . '/../src/Helpers/HtmlSanitizer.php';

$tests = [
    '<p>Safe content</p>',
    '<script>alert("xss");</script><p>Hi</p>',
    '<a href="javascript:alert(1)">click</a>',
    '<img src="data:image/png;base64,AAA..." onerror="alert(1)" />',
    '<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>',
    '<div onclick="doSomething()">click</div>',
    '<a href="/relative/path">relative</a>',
    '<p><strong>bold</strong> and <em>italic</em></p>'
];

foreach ($tests as $i => $html){
    $clean = HtmlSanitizer::sanitize_html($html);
    echo "--- Test #" . ($i+1) . " ---\n";
    echo "Input: " . $html . "\n";
    echo "Output: " . $clean . "\n\n";
}

echo "Done\n";
