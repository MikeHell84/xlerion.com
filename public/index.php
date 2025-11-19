<?php
// Minimal front controller for Vanilla MVC CMR
session_start();

// Determine project root: support two deployment layouts:
// 1) Normal repo: public/index.php -> project root is dirname(__DIR__)
// 2) All files copied into webroot: index.php is in project root -> project root is __DIR__
$projectRoot = __DIR__;
if (!is_dir($projectRoot . '/src')) {
    $projectRoot = dirname(__DIR__);
}

// Load .env simple loader using detected project root
function env($key, $default = null) {
    static $vars = null;
    if ($vars === null) {
        $vars = [];
        global $projectRoot;
        $p = $projectRoot . '/.env';
        if (file_exists($p)) {
            foreach (file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                [$k,$v] = array_map('trim', explode('=', $line, 2) + [1 => null]);
                if ($k) $vars[$k] = trim($v, " \"'\n\r");
            }
        }
    }
    return $vars[$key] ?? $default;
}

require_once $projectRoot . '/src/Router.php';
require_once $projectRoot . '/src/Model/Database.php';
require_once $projectRoot . '/src/Controller/ContactController.php';
require_once $projectRoot . '/src/RateLimiter.php';

$router = new Router();
$router->get('/', function() use ($projectRoot) { include $projectRoot . '/views/home.php'; });
$router->get('/search', function() use ($projectRoot) { include $projectRoot . '/views/search_results.php'; });
$router->get('/contact', [\ContactController::class, 'show']);
$router->post('/contact', [\ContactController::class, 'submit']);
$router->run();
