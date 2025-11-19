<?php
// Generate simple sitemap.xml from cms_pages and blog_posts
chdir(dirname(__DIR__));
$env = parse_ini_file('.env', INI_SCANNER_RAW);
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$base = rtrim($env['APP_URL'] ?? 'https://xlerion.com','/');
$pages = $pdo->query("SELECT slug, updated_at FROM cms_pages WHERE is_published=1")->fetchAll(PDO::FETCH_ASSOC);
$posts = $pdo->query("SELECT slug, published_at FROM blog_posts WHERE status='published'")->fetchAll(PDO::FETCH_ASSOC);
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
foreach ($pages as $p) { $url = $xml->addChild('url'); $url->addChild('loc', $base.'/'.htmlspecialchars($p['slug'])); $url->addChild('lastmod', date('c', strtotime($p['updated_at'] ?? date('Y-m-d')))); }
foreach ($posts as $p) { $url = $xml->addChild('url'); $url->addChild('loc', $base.'/blog/'.htmlspecialchars($p['slug'])); $url->addChild('lastmod', date('c', strtotime($p['published_at'] ?? date('Y-m-d')))); }
$xml->asXML(__DIR__ . '/../public/sitemap.xml');
echo "Sitemap generated\n";
