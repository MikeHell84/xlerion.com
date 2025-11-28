<?php
class Router {
    protected $routes = [];

    public function get($path, $handler) { $this->map('GET', $path, $handler); }
    public function post($path, $handler) { $this->map('POST', $path, $handler); }
    public function put($path, $handler) { $this->map('PUT', $path, $handler); }
    public function delete($path, $handler) { $this->map('DELETE', $path, $handler); }
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
        // Support simple named-parameter routes like '/blog/:slug'
        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            $path = $r['path'];
            if (strpos($path, ':') === false) continue; // no params
            // build regex from path: treat segments starting with ':' as named capture groups
            $segments = explode('/', trim($path, '/'));
            $patternParts = [];
            foreach ($segments as $seg) {
                if ($seg !== '' && $seg[0] === ':') {
                    $name = preg_replace('/[^a-zA-Z0-9_]/', '', substr($seg,1));
                    $patternParts[] = '(?P<' . $name . '>[^/]+)';
                } else {
                    $patternParts[] = preg_quote($seg, '#');
                }
            }
            $pattern = '#^/' . implode('/', $patternParts) . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                // inject named params into $_GET for handlers that rely on them
                foreach ($matches as $k => $v) {
                    if (is_string($k)) $_GET[$k] = $v;
                }
                $h = $r['handler'];
                // collect param values in route path order
                $paramValues = [];
                foreach ($segments as $seg) {
                    if ($seg !== '' && $seg[0] === ':') {
                        $name = preg_replace('/[^a-zA-Z0-9_]/', '', substr($seg,1));
                        $paramValues[] = isset($matches[$name]) ? $matches[$name] : null;
                    }
                }
                if (is_callable($h)) return call_user_func_array($h, $paramValues);
                if (is_array($h) && class_exists($h[0])) {
                    $ctrl = new $h[0]();
                    return call_user_func_array([$ctrl, $h[1]], $paramValues);
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

            // Fallback: try fuzzy match by title using a humanized slug (replace dashes with spaces)
            $human = str_replace('-', ' ', $slug);
            $st2 = $pdo->prepare('SELECT * FROM cms_pages WHERE title LIKE ? AND is_published = 1 LIMIT 1');
            $st2->execute(["%" . $human . "%"]);
            $page2 = $st2->fetch();
            if ($page2) {
                // Redirect to canonical slug to avoid duplicate content
                $canonical = '/' . ltrim($page2['slug'], '/');
                header('Location: ' . $canonical, true, 301);
                // include the page as a fallback for clients that don't follow redirects
                include __DIR__ . '/../views/cms_page.php';
                return;
            }
        } catch (Exception $e) {
            // ignore and fallthrough
        }
        http_response_code(404); echo "404 Not Found";
    }
}
