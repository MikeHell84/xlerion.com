<?php
class Router {
    protected $routes = [];

    public function get($path, $handler) { $this->map('GET', $path, $handler); }
    public function post($path, $handler) { $this->map('POST', $path, $handler); }
    protected function map($method, $path, $handler) {
        $this->routes[] = compact('method','path','handler');
    }
    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];
        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if ($r['path'] === $uri) {
                $h = $r['handler'];
                if (is_callable($h)) return call_user_func($h);
                if (is_array($h) && class_exists($h[0])) {
                    $ctrl = new $h[0]();
                    return call_user_func([$ctrl, $h[1]]);
                }
            }
        }
        // Attempt to serve CMS page by slug (strip leading /)
        $slug = ltrim($uri, '/');
        if ($slug === '') $slug = 'inicio';
        try {
            require_once __DIR__ . '/Model/Database.php';
            $pdo = Database::pdo();
            $st = $pdo->prepare('SELECT * FROM cms_pages WHERE slug = ? AND is_published = 1 LIMIT 1');
            $st->execute([$slug]);
            $page = $st->fetch();
            if ($page) { include __DIR__ . '/../views/cms_page.php'; return; }
        } catch (Exception $e) {
            // ignore and fallthrough
        }
        http_response_code(404); echo "404 Not Found";
    }
}
