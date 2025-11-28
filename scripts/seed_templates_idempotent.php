<?php
// Idempotent seeder for templates
require_once __DIR__ . '/../src/Model/Template.php';

$examples = [
    [
        'name' => 'Basic Layout',
        'description' => 'Simple page with header, content and footer regions.',
        'author_id' => null,
        'data' => [
            'regions' => [
                'header' => '<nav class="site-nav"><a href="/">Home</a> | <a href="/blog">Blog</a></nav>',
                'content' => '<section class="container"><h1>Welcome</h1><p>This is the main content area.</p></section>',
                'footer' => '<small>&copy; '.date('Y').' Xlerion</small>'
            ]
        ]
    ],
    [
        'name' => 'Landing (Hero)',
        'description' => 'Landing page with hero section, features and CTA.',
        'author_id' => null,
        'data' => [
            'regions' => [
                'header' => '<header class="hero"><h1>Product</h1><p>Short pitch here</p><a class="btn" href="#signup">Get started</a></header>',
                'content' => '<section class="features"><div class="col">Feature A</div><div class="col">Feature B</div></section>',
                'footer' => '<footer class="site-footer"><p>Contact: info@example.com</p></footer>',
                'menu' => '<ul class="menu"><li><a href="/">Home</a></li><li><a href="/pricing">Pricing</a></li></ul>'
            ]
        ]
    ],
    [
        'name' => 'Blog Post',
        'description' => 'Template for blog posts with author box and related posts.',
        'author_id' => null,
        'data' => [
            'regions' => [
                'header' => '<div class="post-header"><h1>{{title}}</h1><p class="meta">By {{author}} on {{date}}</p></div>',
                'content' => '<article class="post-body"><p>Intro paragraph...</p><h2>Section</h2><p>Content here</p></article>',
                'footer' => '<div class="post-footer"><div class="author-box">About the author...</div><div class="related">Related posts...</div></div>'
            ]
        ]
    ]
];

// existing slugs
$existing = array_map(function($t){ return $t['slug']; }, array_map(function($r){
    return [
        'slug' => $r['slug']
    ];
}, Template::findAll()));

foreach ($examples as $ex) {
    $slug = $ex['slug'] ?? Template::slugify($ex['name']);
    if (in_array($slug, $existing)) {
        echo "Skipping existing template: {$ex['name']} (slug: $slug)\n";
        continue;
    }
    $id = Template::create($ex);
    echo "Inserted template {$ex['name']} with id: $id\n";
    $existing[] = $slug;
}

echo "Done.\n";
