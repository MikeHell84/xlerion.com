<?php
require_once __DIR__ . '/../src/Helpers/HtmlSanitizer.php';

$tests = [
    'basic' => '<div class="container"><h1 class="display-4">Hello</h1><p onclick="alert(1)">Click me</p></div>',
    'data_uri' => '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA" alt="x">',
    'iframe' => '<iframe src="https://www.youtube.com/embed/VIDEO" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
    'classes' => '<div class="container unknown-class col-md-6 p-3 custom-foo text-primary">Content</div>',
    'data_attrs' => '<div data-toggle="modal" data-info="{\"a\":1}"><span data-custom="x">X</span></div>',
    'javascript' => '<a href="javascript:alert(1)">bad</a>',
    'style' => '<p style="color:red;" class="lead">Styled</p>',
    'complex' => '<section class="section-grid"><div class="tpl-block" onclick="doBad()"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA" class="placeholder-img unknown" /></div></section>'
];

foreach ($tests as $k => $html){
    echo "--- $k ---\n";
    $clean = HtmlSanitizer::sanitize_html($html);
    echo $clean . "\n\n";
}

echo "Done\n";
